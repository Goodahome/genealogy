@section('title')
    &vert; {{ __('app.clan_lineage') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.clan_lineage') }}
        <span class="text-sm font-normal text-neutral-500 dark:text-neutral-400">
            — {{ auth()->user()->currentTeam->name }}
        </span>
    </x-slot>

    <div class="w-full p-2 space-y-5">
        <div class="overflow-x-auto">
            <livewire:people.clan-lineage />
        </div>
    </div>
</x-app-layout>
