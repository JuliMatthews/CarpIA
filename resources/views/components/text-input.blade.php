@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#2a2a2a] bg-[#1e1e1e] text-[#f0f0f0] focus:border-[#7c3aed] focus:ring-[#7c3aed] rounded-md shadow-sm']) }}>
