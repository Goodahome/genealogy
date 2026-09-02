<div class="overflow-x-auto overscroll-x-contain print:hidden" style="-webkit-overflow-scrolling: touch;">
        <div class="flex min-w-full justify-start">
        <nav class="flex items-center gap-1" aria-label="{{ __('person.profile') }}">
            <x-ts-button
                href="/people/{{ $person->id }}"
                color="{{ request()->routeIs('people.show') ? 'yellow' : 'primary' }}"
                xs
                title="{{ __('person.profile') }}"
            >
                <x-ts-icon icon="tabler.id" class="size-3" />
                <span class="whitespace-nowrap">{{ __('person.profile') }}</span>
            </x-ts-button>

            <x-ts-button
                href="/people/{{ $person->id }}/ancestors"
                color="{{ request()->routeIs('people.ancestors') ? 'yellow' : 'secondary' }}"
                xs
                title="{{ __('person.ancestors') }}"
            >
                <x-ts-icon icon="tabler.binary-tree" class="size-3 rotate-180" />
                <span class="whitespace-nowrap">{{ __('person.ancestors') }}</span>
            </x-ts-button>

            <x-ts-button
                href="/people/{{ $person->id }}/descendants"
                color="{{ request()->routeIs('people.descendants') ? 'yellow' : 'secondary' }}"
                xs
                title="{{ __('person.descendants') }}"
            >
                <x-ts-icon icon="tabler.binary-tree" class="size-3" />
                <span class="whitespace-nowrap">{{ __('person.descendants') }}</span>
            </x-ts-button>

            <x-ts-button
                href="/people/{{ $person->id }}/chart"
                color="{{ request()->routeIs('people.chart') ? 'yellow' : 'secondary' }}"
                xs
                title="{{ __('app.family_chart') }}"
            >
                <x-ts-icon icon="tabler.social" class="size-3" />
                <span class="whitespace-nowrap">{{ __('app.family_chart') }}</span>
            </x-ts-button>

            <x-ts-button
                href="/people/{{ $person->id }}/history"
                color="{{ request()->routeIs('people.history') ? 'yellow' : 'cyan' }}"
                xs
                title="{{ __('app.history') }}"
            >
                <x-ts-icon icon="tabler.history" class="size-3" />
                <span class="whitespace-nowrap">{{ __('app.history') }}</span>
            </x-ts-button>

            <x-ts-button
                href="/people/{{ $person->id }}/datasheet"
                color="{{ request()->routeIs('people.datasheet') ? 'yellow' : 'secondary' }}"
                xs
                title="{{ __('app.datasheet') }}"
            >
                <x-ts-icon icon="tabler.pdf" class="size-3" />
                <span class="whitespace-nowrap">{{ __('app.datasheet') }}</span>
            </x-ts-button>
        </nav>
    </div>
</div>
