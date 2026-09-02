<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FamilyFundEntry;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class FamilyFundController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizePermission('fund:read');

        $teamId = (int) auth()->user()->currentTeam->id;
        $type = $request->string('type')->toString();
        $year = $request->integer('year') ?: null;

        $baseQuery = FamilyFundEntry::query()->forTeam($teamId);

        $totalIncome = (clone $baseQuery)->where('type', FamilyFundEntry::TYPE_INCOME)->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', FamilyFundEntry::TYPE_EXPENSE)->sum('amount');
        $balance = (float) $totalIncome - (float) $totalExpense;

        $yearQuery = (clone $baseQuery)->when($year, fn ($q) => $q->whereYear('occurred_on', $year), fn ($q) => $q->whereYear('occurred_on', now()->year));
        $yearIncome = (clone $yearQuery)->where('type', FamilyFundEntry::TYPE_INCOME)->sum('amount');
        $yearExpense = (clone $yearQuery)->where('type', FamilyFundEntry::TYPE_EXPENSE)->sum('amount');

        $entries = FamilyFundEntry::query()
            ->forTeam($teamId)
            ->with(['creator', 'person'])
            ->when(in_array($type, [FamilyFundEntry::TYPE_INCOME, FamilyFundEntry::TYPE_EXPENSE], true), fn ($q) => $q->where('type', $type))
            ->when($year, fn ($q) => $q->whereYear('occurred_on', $year))
            ->orderByDesc('occurred_on')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $availableYears = FamilyFundEntry::query()
            ->forTeam($teamId)
            ->selectRaw('YEAR(occurred_on) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values();

        $donationBase = FamilyFundEntry::query()
            ->forTeam($teamId)
            ->where('type', FamilyFundEntry::TYPE_INCOME)
            ->where('category', 'donation')
            ->when($year, fn ($q) => $q->whereYear('occurred_on', $year));

        $donationRankingRows = (clone $donationBase)
            ->whereNotNull('person_id')
            ->selectRaw('person_id, SUM(amount) as total_amount, COUNT(*) as donation_count')
            ->groupBy('person_id')
            ->orderByDesc('total_amount')
            ->orderBy('person_id')
            ->limit(20)
            ->get();

        $rankedPeople = Person::query()
            ->whereIn('id', $donationRankingRows->pluck('person_id'))
            ->get()
            ->keyBy('id');

        $donationRanking = $donationRankingRows->map(function ($row) use ($rankedPeople) {
            $row->setRelation('person', $rankedPeople->get($row->person_id));

            return $row;
        });

        $unlinkedDonationCount = (clone $donationBase)
            ->whereNull('person_id')
            ->count();

        return view('back.family-fund.index', [
            'entries' => $entries,
            'balance' => $balance,
            'totalIncome' => (float) $totalIncome,
            'totalExpense' => (float) $totalExpense,
            'yearIncome' => (float) $yearIncome,
            'yearExpense' => (float) $yearExpense,
            'type' => $type,
            'year' => $year,
            'availableYears' => $availableYears,
            'donationRanking' => $donationRanking,
            'unlinkedDonationCount' => $unlinkedDonationCount,
        ]);
    }

    public function create(): View
    {
        $this->authorizePermission('fund:create');

        return view('back.family-fund.create', [
            'peopleOptions' => $this->peopleOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizePermission('fund:create');

        $validated = $this->validateEntry($request);

        FamilyFundEntry::create([
            ...$validated,
            'team_id' => auth()->user()->currentTeam->id,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('family-fund.index');
    }

    public function edit(FamilyFundEntry $entry): View
    {
        $this->authorizePermission('fund:update');
        $this->ensureTeamOwns($entry);

        return view('back.family-fund.edit', [
            'entry' => $entry,
            'peopleOptions' => $this->peopleOptions(),
        ]);
    }

    public function update(Request $request, FamilyFundEntry $entry): RedirectResponse
    {
        $this->authorizePermission('fund:update');
        $this->ensureTeamOwns($entry);

        $entry->update($this->validateEntry($request));

        return redirect()
            ->route('family-fund.index');
    }

    public function destroy(FamilyFundEntry $entry): RedirectResponse
    {
        $this->authorizePermission('fund:delete');
        $this->ensureTeamOwns($entry);

        $entry->delete();

        return redirect()
            ->route('family-fund.index');
    }

    private function validateEntry(Request $request): array
    {
        $isDonation = $request->input('type') === FamilyFundEntry::TYPE_INCOME
            && $request->input('category') === 'donation';

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'occurred_on' => 'required|date',
            'title' => 'required|string|max:255',
            'contributor' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'person_id' => [
                Rule::requiredIf($isDonation),
                'nullable',
                'integer',
                'exists:people,id',
            ],
        ], [
            'person_id.required' => __('fund.person_required_for_donation'),
        ]);

        $allowed = array_keys(FamilyFundEntry::categoriesForType($validated['type']));
        if (! in_array($validated['category'], $allowed, true)) {
            abort(422, __('fund.invalid_category'));
        }

        if (! empty($validated['person_id'])) {
            $person = Person::query()
                ->whereKey($validated['person_id'])
                ->where('team_id', auth()->user()->currentTeam->id)
                ->first();

            if (! $person) {
                abort(403);
            }

            if (blank($validated['contributor'] ?? null)) {
                $validated['contributor'] = $person->name;
            }
        } else {
            $validated['person_id'] = null;
        }

        return $validated;
    }

    private function peopleOptions(): Collection
    {
        return Person::query()
            ->where('team_id', auth()->user()->currentTeam->id)
            ->orderBy('surname')
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'surname', 'dob', 'yob', 'dod', 'yod'])
            ->map(fn (Person $person): array => [
                'id' => $person->id,
                'name' => $person->name,
                'label' => sprintf(
                    '%s · %s · #%d',
                    $person->name ?: __('fund.unnamed'),
                    $person->lifetime ?: __('fund.lifetime_unknown'),
                    $person->id
                ),
            ])
            ->values();
    }

    private function authorizePermission(string $permission): void
    {
        if (! auth()->user()->hasPermission($permission)) {
            abort(403);
        }
    }

    private function ensureTeamOwns(FamilyFundEntry $entry): void
    {
        if ((int) $entry->team_id !== (int) auth()->user()->currentTeam->id) {
            abort(403);
        }
    }
}
