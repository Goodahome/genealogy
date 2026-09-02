<?php

declare(strict_types=1);

namespace App\Livewire\People;

use App\Models\Couple;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

final class ClanLineage extends Component
{
    public Collection $descendants;

    public Collection $roots;

    public Collection $spousesMap;

    public Collection $mainClanIds;

    /** @var Collection<int, Collection> main-clan person id => natal relatives of their spouse */
    public Collection $affinalRelativesByPerson;

    public int $count_min = 1;

    public int $count = 5;

    public int $count_max = 128;

    public function mount(): void
    {
        $this->loadDescendants();
    }

    public function increment(): void
    {
        if ($this->count < $this->count_max) {
            $this->count++;
        }
    }

    public function decrement(): void
    {
        if ($this->count > $this->count_min) {
            $this->count--;
        }
    }

    public function render(): View
    {
        return view('livewire.people.clan-lineage');
    }

    private function loadDescendants(): void
    {
        $teamId = auth()->user()->currentTeam->id;

        $this->spousesMap = $this->loadSpousesMap($teamId);
        $this->descendants = collect(DB::select($this->getRecursiveQuery($teamId)));

        $degreeZeroMales = $this->descendants
            ->where('degree', 0)
            ->where('sex', 'm')
            ->values();

        $rootIdByPerson = $this->buildRootIdByPersonMap($degreeZeroMales->pluck('id'));
        $affinalRootIds = $this->detectAffinalRootIds($rootIdByPerson);

        $this->roots = $degreeZeroMales
            ->reject(fn (object $person): bool => $affinalRootIds->contains($person->id))
            ->sortBy([
                ['yob', 'asc'],
                ['dob', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        $this->mainClanIds = $this->collectPatrilinealIds(
            $this->descendants,
            $this->roots->pluck('id'),
        );

        $this->affinalRelativesByPerson = $this->buildAffinalRelativesByPerson();

        $maxDegree = $this->descendants->max('degree') ?? 0;
        $this->count_max = min((int) $maxDegree + 1, $this->count_max);

        if ($this->count > $this->count_max) {
            $this->count = $this->count_max;
        }
    }

    /**
     * 若某女属于根族 A，且嫁给根族 B 的男子，则 A 视为姻亲族（不再作为顶部主族始祖）。
     * 若两族互相嫁娶，保留人数更多（或更早）的一方作为主族。
     */
    private function detectAffinalRootIds(Collection $rootIdByPerson): Collection
    {
        $edges = collect();

        foreach ($this->spousesMap as $personId => $partners) {
            $person = $this->descendants->firstWhere('id', $personId);

            if (! $person || ($person->sex ?? null) !== 'm') {
                continue;
            }

            $husbandRootId = $rootIdByPerson->get($personId);

            if ($husbandRootId === null) {
                continue;
            }

            foreach ($partners as $spouse) {
                $wifeRootId = $rootIdByPerson->get($spouse->id);

                if ($wifeRootId === null || $wifeRootId === $husbandRootId) {
                    continue;
                }

                $edges->push([
                    'wife_root'    => (int) $wifeRootId,
                    'husband_root' => (int) $husbandRootId,
                ]);
            }
        }

        $affinalRootIds = collect();

        foreach ($edges as $edge) {
            if ($this->preferMainRootId($edge['husband_root'], $edge['wife_root']) === $edge['husband_root']) {
                $affinalRootIds->push($edge['wife_root']);
            }
        }

        return $affinalRootIds->unique()->values();
    }

    private function preferMainRootId(int $rootA, int $rootB): int
    {
        $countA = $this->descendants->filter(
            fn (object $person): bool => $person->id === $rootA || str_starts_with($person->sequence, $rootA . ',')
        )->count();

        $countB = $this->descendants->filter(
            fn (object $person): bool => $person->id === $rootB || str_starts_with($person->sequence, $rootB . ',')
        )->count();

        if ($countA !== $countB) {
            return $countA > $countB ? $rootA : $rootB;
        }

        $personA = $this->descendants->firstWhere('id', $rootA);
        $personB = $this->descendants->firstWhere('id', $rootB);

        $yearA = $personA->yob
            ?? (isset($personA->dob) ? (int) substr((string) $personA->dob, 0, 4) : 9999);
        $yearB = $personB->yob
            ?? (isset($personB->dob) ? (int) substr((string) $personB->dob, 0, 4) : 9999);

        if ((int) $yearA !== (int) $yearB) {
            return (int) $yearA <= (int) $yearB ? $rootA : $rootB;
        }

        return min($rootA, $rootB);
    }

    private function buildRootIdByPersonMap(Collection $maleRootIds): Collection
    {
        $map = collect();

        foreach ($this->descendants as $person) {
            foreach ($maleRootIds as $rootId) {
                if ($person->id === $rootId || str_starts_with($person->sequence, $rootId . ',')) {
                    $map->put($person->id, (int) $rootId);

                    break;
                }
            }
        }

        return $map;
    }

    private function buildAffinalRelativesByPerson(): Collection
    {
        $map = collect();

        foreach ($this->mainClanIds as $personId) {
            $spouse = $this->spousesMap->get($personId, collect())->first();

            if (! $spouse || $this->mainClanIds->contains($spouse->id)) {
                continue;
            }

            $relatives = $this->collectNatalRelatives($spouse);

            if ($relatives->isNotEmpty()) {
                $map->put($personId, $relatives);
            }
        }

        return $map;
    }

    /**
     * 配偶娘家：父母 + 兄弟姐妹（不含配偶本人、不含主族成员）。
     */
    private function collectNatalRelatives(object $spouse): Collection
    {
        $relatives = collect();
        $seen = collect();

        $push = function (?object $person) use (&$relatives, &$seen): void {
            if (! $person || $seen->contains($person->id) || $this->mainClanIds->contains($person->id)) {
                return;
            }

            $seen->push($person->id);
            $relatives->push($person);
        };

        $father = $spouse->father_id
            ? $this->descendants->firstWhere('id', $spouse->father_id)
            : null;
        $mother = $spouse->mother_id
            ? $this->descendants->firstWhere('id', $spouse->mother_id)
            : null;

        $push($father);
        $push($mother);

        $siblings = $this->descendants->filter(function (object $person) use ($spouse): bool {
            if ($person->id === $spouse->id) {
                return false;
            }

            if ($spouse->father_id && (int) $person->father_id === (int) $spouse->father_id) {
                return true;
            }

            if ($spouse->mother_id && (int) $person->mother_id === (int) $spouse->mother_id) {
                return true;
            }

            return false;
        })->sortBy([
            ['sex', 'desc'],
            ['dob', 'asc'],
            ['yob', 'asc'],
            ['id', 'asc'],
        ]);

        foreach ($siblings as $sibling) {
            $push($sibling);
        }

        return $relatives->values();
    }

    private function loadSpousesMap(int $teamId): Collection
    {
        $entriesByPerson = collect();

        Couple::query()
            ->where('team_id', $teamId)
            ->with(['person_1', 'person_2'])
            ->orderByDesc('is_married')
            ->orderBy('date_start')
            ->get()
            ->each(function (Couple $couple) use ($entriesByPerson): void {
                if (! $couple->person_1 || ! $couple->person_2) {
                    return;
                }

                foreach ([$couple->person_1, $couple->person_2] as $person) {
                    $partner = $couple->person_1->id === $person->id ? $couple->person_2 : $couple->person_1;

                    $entries = $entriesByPerson->get($person->id, collect());

                    if ($entries->contains(fn (array $entry): bool => $entry['partner']->id === $partner->id)) {
                        continue;
                    }

                    $entries->push([
                        'partner'    => $partner,
                        'is_married' => (bool) $couple->is_married,
                        'has_ended'  => (bool) $couple->has_ended,
                        'date_start' => $couple->date_start?->format('Y-m-d') ?? '9999-12-31',
                    ]);

                    $entriesByPerson->put($person->id, $entries);
                }
            });

        return $entriesByPerson->map(function (Collection $entries): Collection {
            return $entries
                ->sortBy([
                    fn (array $entry): int => $entry['has_ended'] ? 1 : 0,
                    fn (array $entry): int => $entry['is_married'] ? 0 : 1,
                    'date_start',
                ])
                ->pluck('partner')
                ->values();
        });
    }

    private function collectPatrilinealIds(Collection $descendants, Collection $rootIds): Collection
    {
        return $descendants
            ->filter(function (object $person) use ($rootIds): bool {
                foreach ($rootIds as $rootId) {
                    if ($person->id === $rootId || str_starts_with($person->sequence, $rootId . ',')) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('id')
            ->values();
    }

    private function getRecursiveQuery(int $teamId): string
    {
        $countMax = $this->count_max;

        return "
            WITH RECURSIVE team_people AS (
                SELECT id
                FROM people
                WHERE deleted_at IS NULL AND team_id = {$teamId}
            ),
            descendants AS (
                SELECT
                    p.id, p.firstname, p.surname, p.sex, p.father_id, p.mother_id, p.dod, p.yod, p.team_id, p.photo, p.dob, p.yob,
                    0 AS degree,
                    CAST(p.id AS CHAR(1024)) AS sequence
                FROM people p
                WHERE p.deleted_at IS NULL
                    AND p.team_id = {$teamId}
                    AND (p.father_id IS NULL OR p.father_id NOT IN (SELECT id FROM team_people))

                UNION ALL

                SELECT
                    p.id, p.firstname, p.surname, p.sex, p.father_id, p.mother_id, p.dod, p.yod, p.team_id, p.photo, p.dob, p.yob,
                    d.degree + 1 AS degree,
                    CAST(CONCAT(d.sequence, ',', p.id) AS CHAR(1024)) AS sequence
                FROM people p
                JOIN descendants d ON p.father_id = d.id
                WHERE p.deleted_at IS NULL
                    AND p.team_id = {$teamId}
                    AND d.degree < {$countMax}
            )

            SELECT * FROM descendants ORDER BY degree, sex DESC, dob, yob, id;
        ";
    }
}
