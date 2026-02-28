@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-erms-blue text-sm font-medium leading-5 text-erms-text focus:outline-none focus:border-erms-blue transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-erms-muted hover:text-erms-text hover:border-erms-border focus:outline-none focus:text-erms-text focus:border-erms-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
