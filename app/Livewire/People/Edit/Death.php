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
            $validated = $this->deathForm->validate();

            $this->person->update([
                'yod' => $this->deathForm->yod ?? null,
                'dod' => $this->deathForm->dod ?? null,
                'pod' => $this->deathForm->pod ?? null,
                'dod_lunar_year' => $this->deathForm->dod_year,
                'dod_lunar_month' => $this->deathForm->dod_month,
                'dod_lunar_day' => $this->deathForm->dod_day,
                'dod_lunar_leap' => $this->deathForm->dod_leap,
            ]);

            $this->person->updateMetadata(
                collect($validated)
                    ->forget(['yod', 'dod', 'pod'])
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
        return
        $this->deathForm->yod !== $this->person->yod or
        $this->deathForm->dod !== ($this->person->dod ? Carbon::parse($this->person->dod)->format('Y-m-d') : null) or
        $this->deathForm->pod !== $this->person->pod or
        
        // 检查农历死亡日期是否有变化
        $this->deathForm->dod_year !== $this->person->dod_lunar_year or
        $this->deathForm->dod_month !== $this->person->dod_lunar_month or
        $this->deathForm->dod_day !== $this->person->dod_lunar_day or
        $this->deathForm->dod_leap !== $this->person->dod_lunar_leap or

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
}
