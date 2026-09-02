@section('title')
    &vert; {{ __('person.ancestors') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('person.ancestors') }}
    </x-slot>

    <div class="sticky top-[6.5rem] z-20 bg-gray-100 p-2 pb-2 dark:bg-gray-900 sm:pb-4">
        <livewire:people.heading :person="$person" />
    </div>

    <div class="w-full p-2 space-y-5">
        <div class="md:min-w-max md:max-w-sm">
            <div class="overflow-x-auto">
                <livewire:people.ancestors :person="$person" />
            </div>
        </div>
    </div>
</x-app-layout>
