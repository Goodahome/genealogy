<div class="flex h-full min-h-72 flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-200">
    <div class="flex h-24 shrink-0 items-end justify-center p-2 pb-0">
        <x-image.photo :person="$person" />
    </div>

    <div class="flex h-6 shrink-0 items-center px-2 text-sm">
        <div class="min-w-0 flex-1 truncate">
            {!! isset($person->lifetime) ? $person->lifetime : '&nbsp;' !!}
        </div>

        <div class="min-w-0 flex-1 truncate text-end">
            {!! isset($person->age) ? $person->age . ' ' . trans_choice('person.years', $person->age) : '&nbsp;' !!}
        </div>
    </div>

    <div class="min-h-0 flex-1 px-2 pb-0 pt-1 text-sm">
        <x-hr.narrow />

        <p class="flex h-5 min-w-0 items-center gap-1 truncate" title="{{ $person->name }}">
            <x-link href="/people/{{ $person->id }}" @class([
                'min-w-0 truncate',
                'text-red-600 dark:text-red-400' => $person->isDeceased(),
            ])>
                {{ $person->name }}
            </x-link>
            <x-ts-icon icon="tabler.{{ $person->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5 shrink-0" />
        </p>

        <p class="h-5 truncate" title="{{ $person->birthname ? __('person.birthname') . ': ' . $person->birthname : '' }}">
            {{ __('person.birthname') }} : {{ $person->birthname ?: '' }}
        </p>

        <p class="h-5 truncate" title="{{ $person->nickname ? __('person.nickname') . ': ' . $person->nickname : '' }}">
            {{ __('person.nickname') }} : {{ $person->nickname ?: '' }}
        </p>

        <x-hr.narrow />

        <p class="flex h-5 min-w-0 items-center gap-1 truncate" title="{{ $person->father?->name }}">
            <span class="shrink-0">{{ __('person.father') }} :</span>
            @if ($person->father)
                <x-link href="/people/{{ $person->father->id }}" @class([
                    'min-w-0 truncate',
                    'text-red-600 dark:text-red-400' => $person->father->isDeceased(),
                ])>
                    {{ $person->father->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->father->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5 shrink-0" />
            @endif
        </p>

        <p class="flex h-5 min-w-0 items-center gap-1 truncate" title="{{ $person->mother?->name }}">
            <span class="shrink-0">{{ __('person.mother') }} :</span>
            @if ($person->mother)
                <x-link href="/people/{{ $person->mother->id }}" @class([
                    'min-w-0 truncate',
                    'text-red-600 dark:text-red-400' => $person->mother->isDeceased(),
                ])>
                    {{ $person->mother->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->mother->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5 shrink-0" />
            @endif
        </p>

        <x-hr.narrow />
    </div>

    <div class="mt-auto flex shrink-0 flex-wrap p-2 print:hidden">
        <div class="min-w-max max-w-full flex-1 grow">
            <a href="/people/{{ $person->id }}" title="{{ __('app.show_profile') }}">
                <x-ts-button color="primary" class="text-sm">
                    <x-ts-icon icon="tabler.id" class="size-5" />
                    {{ __('person.profile') }}
                </x-ts-button>
            </a>
        </div>

        <div class="min-w-max max-w-full flex-1 grow text-end">
            <a href="/people/{{ $person->id }}/chart">
                <x-ts-button color="secondary" class="text-sm">
                    <x-ts-icon icon="tabler.social" class="size-5" />
                    {{ __('app.family_chart') }}
                </x-ts-button>
            </a>
        </div>
    </div>
</div>
