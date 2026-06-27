@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#7c3aed] text-sm font-medium leading-5 text-[#f0f0f0] focus:outline-none focus:border-[#6d28d9] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#888888] hover:text-[#f0f0f0] hover:border-[#2a2a2a] focus:outline-none focus:text-[#f0f0f0] focus:border-[#2a2a2a] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
