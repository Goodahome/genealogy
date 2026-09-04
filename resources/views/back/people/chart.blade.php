@section('title')
    &vert; {{ __('app.family_chart') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.family_chart') }}
    </x-slot>

    <div class="w-full space-y-5 overflow-x-auto p-2">
        <livewire:people.family-chart :person="$person" />
    </div>
</x-app-layout>
