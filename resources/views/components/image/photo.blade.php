@props(['person' => null])

<div class="user-image flex h-20 w-20 shrink-0 items-center justify-center">
    @if ($person->photo && Storage::exists('public/photos/' . $person->team_id . '/' . $person->photo))
        <img {{ $attributes->merge(['class' => 'h-20 w-20 rounded-sm object-cover shadow-lg dark:shadow-black/30']) }} src="{{ asset('storage/photos/' . $person->team_id . '/' . $person->photo) }}"
        alt="{{ $person->name }}" title="{{ $person->name }}" />
    @else
        <x-svg.person-placeholder :sex="$person->sex ?? 'm'" {{ $attributes->merge(['class' => 'h-20 w-20 rounded-full shadow-lg dark:shadow-black/30 fill-neutral-400']) }}
                               alt="no-image-found" />
    @endif

    @if ($person->isDeceased())
        <div class="ribbon text-xs">{{ __('person.deceased') }}</div>
    @endif
</div>