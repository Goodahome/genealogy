<div
    x-data
    x-effect="
        if ($store.personPanels && $store.personPanels.active === 'chart' && ! $wire.loaded) {
            $wire.loadChart();
        }
    "
>
    @if ($loaded)
        @include('partials.people.family-chart', [
            'person' => $person,
            'compact' => $compact,
        ])
    @endif
</div>
