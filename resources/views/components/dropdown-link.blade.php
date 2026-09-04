@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full px-4 py-2 text-left text-sm leading-5 text-[var(--zp-seal)] hover:bg-[rgba(178,58,45,0.08)] focus:outline-hidden focus:bg-[rgba(178,58,45,0.08)] transition duration-150 ease-in-out'
            : 'block w-full px-4 py-2 text-left text-sm leading-5 text-[var(--zp-ink)] hover:bg-[rgba(239,209,151,0.35)] focus:outline-hidden focus:bg-[rgba(239,209,151,0.35)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
