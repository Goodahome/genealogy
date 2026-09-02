@section('title')
    &vert; {{ __('app.datasheet') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.datasheet') }}
    </x-slot>

    <div class="sticky top-[6.5rem] z-20 bg-gray-100 p-2 pb-2 dark:bg-gray-900 sm:pb-4">
        <livewire:people.heading :person="$person" />
    </div>

    <div class="w-full p-2 space-y-5 overflow-x-auto">
        <livewire:people.datasheet :person="$person" />
    </div>
</x-app-layout>
