@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-erms-blue text-start text-base font-medium text-erms-blue bg-erms-blue/5 focus:outline-none focus:text-erms-blue focus:bg-erms-blue/10 focus:border-erms-blue transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-erms-muted hover:text-erms-text hover:bg-erms-surface-2 hover:border-erms-border focus:outline-none focus:text-erms-text focus:bg-erms-surface-2 focus:border-erms-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
