@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'zp-nav-link zp-wood zp-wood-active mb-1 block w-full border-l-4 border-[var(--zp-seal)] py-2 pl-3 pr-4 text-left text-base font-medium transition duration-150 ease-in-out'
            : 'zp-nav-link zp-wood zp-wood-text mb-1 block w-full border-l-4 border-transparent py-2 pl-3 pr-4 text-left text-base font-medium transition duration-150 ease-in-out hover:border-[rgba(210,170,90,0.55)]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
