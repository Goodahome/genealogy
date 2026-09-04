<?php

declare(strict_types=1);

namespace App\Livewire\People;

use Illuminate\View\View;
use Livewire\Component;

final class FamilyChart extends Component
{
    public $person;

    public bool $compact = false;

    /** Once opened, keep rendered so toggling off/on stays snappy. */
    public bool $loaded = false;

    public function mount(): void
    {
        if (request()->routeIs('people.chart') || request()->query('panel') === 'chart') {
            $this->loaded = true;
        }
    }

    public function loadChart(): void
    {
        $this->loaded = true;
    }

    public function render(): View
    {
        return view('livewire.people.family-chart');
    }
}
