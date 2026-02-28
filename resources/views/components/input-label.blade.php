@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-erms-muted']) }}>
    {{ $value ?? $slot }}
</label>
