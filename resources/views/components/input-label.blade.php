@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#888888]']) }}>
    {{ $value ?? $slot }}
</label>
