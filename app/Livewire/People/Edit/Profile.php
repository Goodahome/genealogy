<?php

declare(strict_types=1);

namespace App\Livewire\People\Edit;

use App\Livewire\Forms\People\ProfileForm;
use App\Livewire\Traits\TrimStringsAndConvertEmptyStringsToNull;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

final class Profile extends Component
{
    use Interactions;
    use TrimStringsAndConvertEmptyStringsToNull;

    public Person $person;

    public ProfileForm $profileForm;

    public bool $asModal = false;

    public function mount(): void
    {
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $this->profileForm->firstname = $this->person->firstname;
        $this->profileForm->surname = $this->person->surname;
        $this->profileForm->occupation = $this->person->occupation;
        $this->profileForm->nickname = $this->person->nickname;

        $this->profileForm->sex = match (mb_strtolower((string) ($this->person->sex ?? ''))) {
            'm', 'male' => 'm',
            'f', 'female' => 'f',
            default => null,
        };
        $this->profileForm->gender_id = $this->person->gender_id;

        // 阳历：有完整 dob 则拆成年月日；否则仅用 yob 填年份
        if ($this->person->dob) {
            $dob = Carbon::parse($this->person->dob);
            $this->profileForm->solar_year = (int) $dob->format('Y');
            $this->profileForm->solar_month = (int) $dob->format('n');
            $this->profileForm->solar_day = (int) $dob->format('j');
        } else {
            $this->profileForm->solar_year = $this->person->yob !== null ? (int) $this->person->yob : null;
            $this->profileForm->solar_month = null;
            $this->profileForm->solar_day = null;
        }

        // 兼容字段（不再展示，供脏检查/旧逻辑对照）
        $this->profileForm->yob = $this->person->yob ?? null;
        $this->profileForm->dob = $this->person->dob ? Carbon::parse($this->person->dob)->format('Y-m-d') : null;

        $this->profileForm->pob = $this->person->pob;
        $this->profileForm->summary = $this->person->summary;

        $this->profileForm->dob_year = $this->person->dob_lunar_year;
        $this->profileForm->dob_month = $this->person->dob_lunar_month;
        $this->profileForm->dob_day = $this->person->dob_lunar_day;
        $this->profileForm->dob_leap = (bool) $this->person->dob_lunar_leap;
    }

    public function saveProfile(): void
    {
        if (! $this->isDirty()) {
            return;
        }

        $validated = $this->profileForm->validate();
        $this->profileForm->assertSolarBirthValid();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $birth = $this->profileForm->resolveSolarBirth();

        $this->person->update([
            'firstname' => $validated['firstname'] ?? null,
            'surname' => $validated['surname'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'nickname' => $validated['nickname'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'gender_id' => $validated['gender_id'] ?? null,
            'yob' => $birth['yob'],
            'dob' => $birth['dob'],
            'pob' => $validated['pob'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'dob_lunar_year' => $validated['dob_year'] ?? null,
            'dob_lunar_month' => $validated['dob_month'] ?? null,
            'dob_lunar_day' => $validated['dob_day'] ?? null,
            'dob_lunar_leap' => $validated['dob_leap'] ?? false,
        ]);

        $this->person->refresh();
        $this->loadForm();
        $this->dispatch('person_updated');
        $this->toast()->success(__('app.save'), __('app.saved'))->send();

        if ($this->asModal) {
            $this->js('$modalClose("edit-person-profile")');
        } else {
            $this->redirect('/people/' . $this->person->id);
        }
    }

    public function resetProfile(): void
    {
        $this->loadForm();

        if ($this->asModal) {
            $this->js('$modalClose("edit-person-profile")');
        }
    }

    public function isDirty(): bool
    {
        [$solarYear, $solarMonth, $solarDay] = $this->currentSolarParts();

        return
            $this->profileForm->firstname !== $this->person->firstname or
            $this->profileForm->surname !== $this->person->surname or
            $this->profileForm->occupation !== $this->person->occupation or
            $this->profileForm->nickname !== $this->person->nickname or
            $this->profileForm->sex !== $this->person->sex or
            $this->profileForm->gender_id !== $this->person->gender_id or
            $this->normalizeInt($this->profileForm->solar_year) !== $solarYear or
            $this->normalizeInt($this->profileForm->solar_month) !== $solarMonth or
            $this->normalizeInt($this->profileForm->solar_day) !== $solarDay or
            $this->profileForm->pob !== $this->person->pob or
            $this->normalizeInt($this->profileForm->dob_year) !== $this->normalizeInt($this->person->dob_lunar_year) or
            $this->normalizeInt($this->profileForm->dob_month) !== $this->normalizeInt($this->person->dob_lunar_month) or
            $this->normalizeInt($this->profileForm->dob_day) !== $this->normalizeInt($this->person->dob_lunar_day) or
            (bool) $this->profileForm->dob_leap !== (bool) $this->person->dob_lunar_leap or
            $this->profileForm->summary !== $this->person->summary;
    }

    public function render(): View
    {
        return view('livewire.people.edit.profile');
    }

    /**
     * @return array{0: ?int, 1: ?int, 2: ?int}
     */
    private function currentSolarParts(): array
    {
        if ($this->person->dob) {
            $dob = Carbon::parse($this->person->dob);

            return [
                (int) $dob->format('Y'),
                (int) $dob->format('n'),
                (int) $dob->format('j'),
            ];
        }

        return [
            $this->person->yob !== null ? (int) $this->person->yob : null,
            null,
            null,
        ];
    }

    private function normalizeInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
