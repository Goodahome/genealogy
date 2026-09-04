<?php

declare(strict_types=1);

namespace App\Livewire\People\Edit;

use App\Livewire\Forms\People\DeathForm;
use App\Livewire\Traits\TrimStringsAndConvertEmptyStringsToNull;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

final class Death extends Component
{
    use Interactions;
    use TrimStringsAndConvertEmptyStringsToNull;

    // -----------------------------------------------------------------------
    public Person $person;

    public DeathForm $deathForm;

    public bool $asModal = false;

    // -----------------------------------------------------------------------
    public function mount(): void
    {
        $this->deathForm->yod = $this->person->yod;
        $this->deathForm->dod = $this->person->dod ? Carbon::parse($this->person->dod)->format('Y-m-d') : null;

        if ($this->person->dod) {
            $dod = Carbon::parse($this->person->dod);
            $this->deathForm->solar_year = (int) $dod->format('Y');
            $this->deathForm->solar_month = (int) $dod->format('n');
            $this->deathForm->solar_day = (int) $dod->format('j');
        } else {
            $this->deathForm->solar_year = $this->person->yod !== null ? (int) $this->person->yod : null;
            $this->deathForm->solar_month = null;
            $this->deathForm->solar_day = null;
        }

        $this->deathForm->pod = $this->person->pod;

        $this->deathForm->dod_year = $this->person->dod_lunar_year;
        $this->deathForm->dod_month = $this->person->dod_lunar_month;
        $this->deathForm->dod_day = $this->person->dod_lunar_day;
        $this->deathForm->dod_leap = (bool) $this->person->dod_lunar_leap;

        $this->deathForm->cemetery_location_name      = $this->person->getMetadataValue('cemetery_location_name');
        $this->deathForm->cemetery_location_address   = $this->person->getMetadataValue('cemetery_location_address');
        $this->deathForm->cemetery_location_latitude  = $this->person->getMetadataValue('cemetery_location_latitude');
        $this->deathForm->cemetery_location_longitude = $this->person->getMetadataValue('cemetery_location_longitude');
    }

    public function saveDeath(): void
    {
        if ($this->isDirty()) {
            $death = $this->deathForm->resolveSolarDeath();
            $this->deathForm->yod = $death['yod'];
            $this->deathForm->dod = $death['dod'];
            $this->deathForm->assertSolarDeathValid();

            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }

            $validated = $this->deathForm->validate();

            $this->person->update([
                'yod' => $death['yod'],
                'dod' => $death['dod'],
                'pod' => $this->deathForm->pod ?? null,
                'dod_lunar_year' => $this->deathForm->dod_year,
                'dod_lunar_month' => $this->deathForm->dod_month,
                'dod_lunar_day' => $this->deathForm->dod_day,
                'dod_lunar_leap' => $this->deathForm->dod_leap,
            ]);

            $this->person->updateMetadata(
                collect($validated)
                    ->forget([
                        'yod', 'dod', 'pod',
                        'solar_year', 'solar_month', 'solar_day',
                        'dod_year', 'dod_month', 'dod_day', 'dod_leap',
                    ])
                    ->filter(fn ($value, $key): bool => $value !== $this->person->getMetadataValue($key))
            );

            $this->person->refresh();
            $this->mount();
            $this->dispatch('person_updated');

            $this->toast()->success(__('app.save'), __('app.saved'))->send();

            if ($this->asModal) {
                $this->js('$modalClose("edit-person-death")');
            }
        }
    }

    public function resetDeath(): void
    {
        $this->mount();

        if ($this->asModal) {
            $this->js('$modalClose("edit-person-death")');
        }
    }

    public function isDirty(): bool
    {
        [$solarYear, $solarMonth, $solarDay] = $this->currentSolarParts();

        return
            $this->normalizeInt($this->deathForm->solar_year) !== $solarYear or
            $this->normalizeInt($this->deathForm->solar_month) !== $solarMonth or
            $this->normalizeInt($this->deathForm->solar_day) !== $solarDay or
            $this->deathForm->pod !== $this->person->pod or
            $this->normalizeInt($this->deathForm->dod_year) !== $this->normalizeInt($this->person->dod_lunar_year) or
            $this->normalizeInt($this->deathForm->dod_month) !== $this->normalizeInt($this->person->dod_lunar_month) or
            $this->normalizeInt($this->deathForm->dod_day) !== $this->normalizeInt($this->person->dod_lunar_day) or
            (bool) $this->deathForm->dod_leap !== (bool) $this->person->dod_lunar_leap or
            $this->deathForm->cemetery_location_name !== $this->person->getMetadataValue('cemetery_location_name') or
            $this->deathForm->cemetery_location_address !== $this->person->getMetadataValue('cemetery_location_address') or
            $this->deathForm->cemetery_location_latitude !== $this->person->getMetadataValue('cemetery_location_latitude') or
            $this->deathForm->cemetery_location_longitude !== $this->person->getMetadataValue('cemetery_location_longitude');
    }

    // ------------------------------------------------------------------------------
    public function render(): View
    {
        return view('livewire.people.edit.death');
    }

    /**
     * @return array{0: ?int, 1: ?int, 2: ?int}
     */
    private function currentSolarParts(): array
    {
        if ($this->person->dod) {
            $dod = Carbon::parse($this->person->dod);

            return [
                (int) $dod->format('Y'),
                (int) $dod->format('n'),
                (int) $dod->format('j'),
            ];
        }

        return [
            $this->person->yod !== null ? (int) $this->person->yod : null,
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
