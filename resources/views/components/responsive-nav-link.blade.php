@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#7c3aed] text-start text-base font-medium text-[#a78bfa] bg-[#7c3aed]/10 focus:outline-none focus:text-[#a78bfa] focus:bg-[#7c3aed]/20 focus:border-[#6d28d9] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#888888] hover:text-[#f0f0f0] hover:bg-[#1e1e1e] hover:border-[#2a2a2a] focus:outline-none focus:text-[#f0f0f0] focus:bg-[#1e1e1e] focus:border-[#2a2a2a] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
