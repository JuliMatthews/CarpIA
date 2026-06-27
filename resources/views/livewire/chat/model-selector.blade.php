<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    {{-- Trigger --}}
    <button
        @click="open = !open"
        class="flex items-center gap-2 px-3 py-1.5 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-sm text-[#f0f0f0] hover:border-[#7c3aed] transition-colors"
    >
        <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <span class="truncate max-w-[200px]">{{ $this->getSelectedModelName() }}</span>
        <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition
        class="absolute top-full left-0 mt-2 w-80 bg-[#161616] border border-[#2a2a2a] rounded-xl shadow-xl z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="max-h-96 overflow-y-auto">
            @forelse($providers as $provider)
                <div class="p-2">
                    <div class="px-3 py-1.5 text-xs font-semibold text-[#888888] uppercase tracking-wider">
                        {{ $provider['name'] }}
                    </div>
                    @foreach($provider['models'] as $model)
                        <button
                            wire:click="selectModel({{ $model['id'] }})"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left {{ $selectedModelId == $model['id'] ? 'bg-[#7c3aed]/20 text-[#a78bfa]' : 'text-[#f0f0f0] hover:bg-[#1e1e1e]' }} transition-colors"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">{{ $model['name'] }}</div>
                                <div class="text-xs text-[#888888] truncate">{{ $model['context_window'] ?? 'N/A' }} tokens</div>
                            </div>
                            @if($selectedModelId == $model['id'])
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-green-500/20 text-green-400 rounded">Activo</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-blue-500/20 text-blue-400 rounded">Usar</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @empty
                <div class="p-4 text-center text-sm text-[#888888]">
                    No hay modelos disponibles. Configura tus API keys.
                </div>
            @endforelse
        </div>
    </div>
</div>
