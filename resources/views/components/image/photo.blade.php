@props(['person' => null])

<div class="user-image flex justify-center">
    @if ($person->photo && Storage::exists('public/photos/' . $person->team_id . '/' . $person->photo))
        <img {{ $attributes->merge(['class' => 'w-20 h-20 rounded-sm shadow-lg dark:shadow-black/30']) }} src="{{ asset('storage/photos/' . $person->team_id . '/' . $person->photo) }}"
        alt="{{ $person->name }}" title="{{ $person->name }}" />
    @else
        <x-svg.person-no-image {{ $attributes->merge(['class' => 'w-12 h-12 rounded-full shadow-lg dark:shadow-black/30 fill-neutral-400']) }}
                               alt="no-image-found" />
    @endif

    @if ($person->isDeceased())
        <div class="ribbon text-xs">{{ __('person.deceased') }}</div>
    @endif
</div>