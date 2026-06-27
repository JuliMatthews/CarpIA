<div>
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-2">
        <div class="text-xs font-semibold text-[#888888] uppercase tracking-wider">Biblioteca</div>
        <button
            wire:click="openCreateModal"
            class="p-1 text-[#888888] hover:text-[#7c3aed] transition-colors"
            title="Nuevo prompt"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>

    {{-- Search --}}
    <div class="px-4 pb-2">
        <input
            type="text"
            placeholder="Buscar prompts..."
            wire:model.live="search"
            class="w-full px-3 py-1.5 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-xs text-[#f0f0f0] placeholder-[#888888] focus:border-[#7c3aed] focus:outline-none"
        >
    </div>

    {{-- Category filter --}}
    <div class="px-4 pb-2 flex flex-wrap gap-1">
        <button
            wire:click="$set('selectedCategory', 'all')"
            class="px-2 py-0.5 text-[10px] rounded {{ $selectedCategory === 'all' ? 'bg-[#7c3aed] text-white' : 'bg-[#1e1e1e] text-[#888888] hover:text-[#f0f0f0]' }} transition-colors"
        >
            Todos
        </button>
        @foreach($categories as $key => $label)
            <button
                wire:click="$set('selectedCategory', '{{ $key }}')"
                class="px-2 py-0.5 text-[10px] rounded {{ $selectedCategory === $key ? 'bg-[#7c3aed] text-white' : 'bg-[#1e1e1e] text-[#888888] hover:text-[#f0f0f0]' }} transition-colors"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Prompts list --}}
    <div class="px-4 pb-2 space-y-1 max-h-64 overflow-y-auto">
        @forelse($prompts as $prompt)
            <div class="group p-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg hover:border-[#7c3aed] transition-colors">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-[#f0f0f0] truncate">{{ $prompt['title'] }}</div>
                        <div class="text-[10px] text-[#888888] truncate mt-0.5">{{ $prompt['content'] }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-1 py-0.5 text-[9px] bg-[#7c3aed]/20 text-[#a78bfa] rounded">{{ $categories[$prompt['category']] ?? $prompt['category'] }}</span>
                            @if($prompt['is_public'])
                                <span class="px-1 py-0.5 text-[9px] bg-green-500/20 text-green-400 rounded">Público</span>
                            @endif
                            @if($prompt['use_count'] > 0)
                                <span class="text-[9px] text-[#888888]">usado {{ $prompt['use_count'] }}x</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                            wire:click="usePrompt({{ $prompt['id'] }})"
                            class="p-1 text-[#888888] hover:text-[#7c3aed] transition-colors"
                            title="Usar prompt"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                        <button
                            wire:click="openEditModal({{ $prompt['id'] }})"
                            class="p-1 text-[#888888] hover:text-yellow-400 transition-colors"
                            title="Editar"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            wire:click="delete({{ $prompt['id'] }})"
                            wire:confirm="¿Eliminar este prompt?"
                            class="p-1 text-[#888888] hover:text-red-400 transition-colors"
                            title="Eliminar"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-xs text-[#888888]">
                No hay prompts guardados
            </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/75" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-[#161616] border border-[#2a2a2a] rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">
                        {{ $isEditing ? 'Editar prompt' : 'Nuevo prompt' }}
                    </h3>

                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm text-[#888888] mb-1">Título</label>
                            <input
                                type="text"
                                wire:model="title"
                                class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none"
                                placeholder="Ej: Explicar concepto"
                            >
                            @error('title') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-[#888888] mb-1">Contenido</label>
                            <textarea
                                wire:model="content"
                                rows="4"
                                class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none resize-none"
                                placeholder="Escribe tu prompt aquí..."
                            ></textarea>
                            @error('content') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-sm text-[#888888] mb-1">Categoría</label>
                                <select
                                    wire:model="category"
                                    class="w-full px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:outline-none"
                                >
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="isPublic" class="rounded border-[#2a2a2a] bg-[#1e1e1e] text-[#7c3aed] focus:ring-[#7c3aed]">
                                    <span class="text-sm text-[#888888]">Público</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button
                                type="button"
                                wire:click="$set('showModal', false)"
                                class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-lg transition-colors"
                            >
                                {{ $isEditing ? 'Guardar cambios' : 'Crear prompt' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
