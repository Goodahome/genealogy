@section('title')
    &vert; {{ __('person.descendants') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('person.descendants') }}
    </x-slot>

    <div class="sticky top-[6.5rem] z-20 bg-gray-100 p-2 pb-2 dark:bg-gray-900 sm:pb-4">
        <livewire:people.heading :person="$person" />
    </div>

    <div class="w-full p-2 space-y-5">
        <div class="md:min-w-max md:max-w-sm">
            <div class="overflow-x-auto">
                <livewire:people.descendants :person="$person" />
            </div>
        </div>
    </div>
</x-app-layout>
