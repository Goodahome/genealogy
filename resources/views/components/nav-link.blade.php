@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'zp-nav-link zp-wood zp-wood-active inline-flex items-center'
            : 'zp-nav-link zp-wood zp-wood-text inline-flex items-center hover:text-[var(--zp-wood-text-active)]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
