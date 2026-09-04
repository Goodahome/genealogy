@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => $disabled
        ? 'block w-full rounded-sm border-[var(--zp-line)] bg-transparent text-[var(--zp-muted)] shadow-none cursor-not-allowed'
        : 'block w-full rounded-sm border-[var(--zp-line)] bg-transparent text-[var(--zp-ink)] focus:border-[var(--zp-gold)] focus:ring-[rgba(196,163,90,0.45)] shadow-none',
]) !!}>
