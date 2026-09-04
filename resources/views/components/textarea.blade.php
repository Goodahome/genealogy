@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => $disabled
        ? 'block w-full min-h-[auto] rounded-sm border-[var(--zp-line)] bg-transparent text-[var(--zp-muted)] shadow-none px-3 py-[0.32rem] cursor-not-allowed peer'
        : 'block w-full min-h-[auto] rounded-sm border-[var(--zp-line)] bg-transparent text-[var(--zp-ink)] focus:border-[var(--zp-gold)] focus:ring-[rgba(196,163,90,0.45)] shadow-none px-3 py-[0.32rem] peer',
]) !!}>
    {{ $slot }}
</textarea>
