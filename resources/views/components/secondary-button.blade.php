<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-md font-semibold text-xs text-[#f0f0f0] uppercase tracking-widest shadow-sm hover:bg-[#2a2a2a] focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:ring-offset-2 focus:ring-offset-[#0d0d0d] disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
