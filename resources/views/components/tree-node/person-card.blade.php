@props(['person', 'compact' => false])

@php
    $name = implode(' ', array_filter([$person->firstname ?? '', $person->surname ?? '']));
    $deceased = ($person->dod ?? null) || ($person->yod ?? null);
    $photoPath = ($person->photo ?? null) && Storage::exists('public/photos/' . $person->team_id . '/' . $person->photo)
        ? asset('storage/photos-096/' . $person->team_id . '/' . $person->photo)
        : null;
@endphp

<x-link
    href="/people/{{ $person->id }}"
    title="{{ ($person->sex ?? '') === 'm' ? __('app.male') : __('app.female') }} — {{ $name }}"
    @class([
        'tree-person-card',
        'tree-person-card--compact' => $compact,
    ])
>
    <figure class="tree-person-card__figure">
        <div class="tree-person-card__avatar user-image">
            @if ($photoPath)
                <img src="{{ $photoPath }}" class="tree-person-card__photo" alt="{{ $name }}" />
            @else
                <x-svg.person-placeholder :sex="$person->sex ?? 'm'" class="tree-person-card__photo tree-person-card__placeholder" alt="no-image-found" />
            @endif

            @if ($deceased)
                <div class="ribbon">{{ __('person.deceased') }}</div>
            @endif
        </div>

        <figcaption @class([
            'tree-person-card__name',
            'text-red-600 dark:text-red-400' => $deceased,
            'text-primary-500 dark:text-primary-300' => ! $deceased,
        ])>
            {{ $name }}
        </figcaption>
    </figure>
</x-link>
