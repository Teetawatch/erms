@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-erms-border text-erms-text placeholder-erms-muted focus:border-erms-blue focus:ring-2 focus:ring-erms-blue/10 rounded-lg']) }}>
