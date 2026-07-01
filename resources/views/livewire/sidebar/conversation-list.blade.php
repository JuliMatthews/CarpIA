<div>
    {{-- Search input --}}
    <div class="px-4 pb-2">
        <div class="relative">
            <input
                type="text"
                placeholder="Buscar..."
                wire:model.live="search"
                class="w-full px-4 py-2 pl-10 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm placeholder-[#888888] focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] focus:outline-none"
            >
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    {{-- Conversations list --}}
    <div class="flex-1 overflow-y-auto px-4 py-2">
        <div class="text-xs font-semibold text-[#888888] uppercase tracking-wider px-2 mb-2">Historial</div>
        <nav class="space-y-1">
            @forelse($conversations as $conversation)
                <div class="group flex items-center gap-1">
                    <a
                        href="{{ route('chat.show', $conversation->id) }}"
                        class="flex-1 flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->route('id') == $conversation->id ? 'bg-[#7c3aed]/20 text-[#a78bfa]' : 'text-[#f0f0f0] hover:bg-[#1e1e1e]' }} transition-colors text-sm"
                    >
                        <svg class="w-4 h-4 text-[#888888] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="truncate">{{ $conversation->title ?: 'Nueva conversación' }}</span>
                    </a>
                    <button
                        wire:click="toggleFavorite({{ $conversation->id }})"
                        class="p-1 opacity-0 group-hover:opacity-100 {{ $this->isFavorite($conversation->id) ? 'text-yellow-400 opacity-100' : 'text-[#888888] hover:text-yellow-400' }} transition-all"
                        title="{{ $this->isFavorite($conversation->id) ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                    >
                        <svg class="w-4 h-4" fill="{{ $this->isFavorite($conversation->id) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                    <div x-data="{ showDelete: false }">
                        <button
                            @click="showDelete = true"
                            class="p-1 opacity-0 group-hover:opacity-100 text-[#888888] hover:text-red-400 transition-all"
                            title="Eliminar conversación"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                            <div @click.away="showDelete = false" class="bg-[#161616] border border-[#2a2a2a] rounded-xl p-6 max-w-sm mx-4 shadow-xl">
                                <h3 class="text-[#f0f0f0] text-lg font-semibold mb-2">Eliminar conversación</h3>
                                <p class="text-[#888888] text-sm mb-6">¿Estás seguro? Esta acción no se puede deshacer.</p>
                                <div class="flex justify-end gap-3">
                                    <button @click="showDelete = false" class="px-4 py-2 text-[#888888] hover:text-[#f0f0f0] text-sm transition-colors">Cancelar</button>
                                    <button @click="showDelete = false; $wire.deleteConversation({{ $conversation->id }})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex items-center gap-3 px-3 py-2 text-[#888888] text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span>Sin conversaciones aún</span>
                </div>
            @endforelse
        </nav>
    </div>
</div>
