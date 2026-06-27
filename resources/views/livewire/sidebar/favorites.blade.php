<div>
    <div class="text-xs font-semibold text-[#888888] uppercase tracking-wider px-4 mb-2">Favoritos</div>
    <nav class="space-y-1 px-4">
        @forelse($favorites as $conversation)
            @if($conversation)
                <a
                    href="{{ route('chat.show', $conversation['id']) }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->route('id') == $conversation['id'] ? 'bg-[#7c3aed]/20 text-[#a78bfa]' : 'text-[#f0f0f0] hover:bg-[#1e1e1e]' }} transition-colors text-sm group"
                >
                    <svg class="w-4 h-4 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <span class="truncate">{{ $conversation['title'] ?: 'Sin título' }}</span>
                </a>
            @endif
        @empty
            <div class="flex items-center gap-3 px-3 py-2 text-[#888888] text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span>Sin favoritos</span>
            </div>
        @endforelse
    </nav>
</div>
