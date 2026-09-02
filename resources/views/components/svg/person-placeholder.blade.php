@props(['sex' => 'm'])

@php
    $isFemale = ($sex ?? 'm') === 'f';
    $src = $isFemale
        ? asset('img/person-no-image-female.png')
        : asset('img/person-no-image-male.png');
@endphp

<img
    src="{{ $src }}"
    alt="no-image-found"
    {{ $attributes->class(['object-cover']) }}
/>
