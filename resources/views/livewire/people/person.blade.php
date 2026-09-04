<article class="zp-wall-sheet text-[#2a2118]">
    <div class="zp-wall-sheet__face flex h-full flex-col">
        <div class="flex h-20 shrink-0 items-end justify-center px-2.5 pb-0 pt-3">
            <x-image.photo :person="$person" />
        </div>

        <div class="flex h-5 shrink-0 items-center px-2.5 text-xs">
            <div class="min-w-0 flex-1 truncate">
                {!! isset($person->lifetime) ? $person->lifetime : '&nbsp;' !!}
            </div>

            <div class="min-w-0 flex-1 truncate text-end">
                {!! isset($person->age) ? $person->age . ' ' . trans_choice('person.years', $person->age) : '&nbsp;' !!}
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-hidden px-2.5 pb-0 pt-0.5 text-xs leading-snug">
            <x-hr.narrow />

            <p class="flex h-5 min-w-0 items-center gap-1 truncate" title="{{ $person->name }}">
                <x-link href="/people/{{ $person->id }}" @class([
                    'min-w-0 truncate',
                    'text-red-600 dark:text-red-400' => $person->isDeceased(),
                ])>
                    {{ $person->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-4 shrink-0" />
            </p>

            <p class="h-4 truncate" title="{{ $person->nickname ? __('person.nickname') . ': ' . $person->nickname : '' }}">
                {{ __('person.nickname') }} : {{ $person->nickname ?: '' }}
            </p>

            <p class="h-4 truncate" title="{{ $person->occupation ? __('person.occupation') . ': ' . $person->occupation : '' }}">
                {{ __('person.occupation') }} : {{ $person->occupation ?: '' }}
            </p>

            <x-hr.narrow />

            <p class="flex h-4 min-w-0 items-center gap-1 truncate" title="{{ $person->father?->name }}">
                <span class="shrink-0">{{ __('person.father') }} :</span>
                @if ($person->father)
                    <x-link href="/people/{{ $person->father->id }}" @class([
                        'min-w-0 truncate',
                        'text-red-600 dark:text-red-400' => $person->father->isDeceased(),
                    ])>
                        {{ $person->father->name }}
                    </x-link>
                    <x-ts-icon icon="tabler.{{ $person->father->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-4 shrink-0" />
                @endif
            </p>

            <p class="flex h-4 min-w-0 items-center gap-1 truncate" title="{{ $person->mother?->name }}">
                <span class="shrink-0">{{ __('person.mother') }} :</span>
                @if ($person->mother)
                    <x-link href="/people/{{ $person->mother->id }}" @class([
                        'min-w-0 truncate',
                        'text-red-600 dark:text-red-400' => $person->mother->isDeceased(),
                    ])>
                        {{ $person->mother->name }}
                    </x-link>
                    <x-ts-icon icon="tabler.{{ $person->mother->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-4 shrink-0" />
                @endif
            </p>

            <x-hr.narrow />
        </div>

        <div class="zp-sheet-actions mt-auto flex shrink-0 gap-1 px-2 pb-2 pt-1 print:hidden">
            <a href="/people/{{ $person->id }}"
               title="{{ __('app.show_profile') }}"
               class="zp-sheet-btn">
                <x-ts-icon icon="tabler.id" class="size-3.5 shrink-0" />
                <span class="truncate">{{ __('person.profile') }}</span>
            </a>

            <a href="/people/{{ $person->id }}/chart"
               title="{{ __('app.family_chart') }}"
               class="zp-sheet-btn">
                <x-ts-icon icon="tabler.social" class="size-3.5 shrink-0" />
                <span class="truncate">{{ __('app.family_chart') }}</span>
            </a>
        </div>
    </div>
</article>
