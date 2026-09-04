@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[var(--zp-ink-soft)]']) }}>
    {{ $value ?? $slot }}
</label>
