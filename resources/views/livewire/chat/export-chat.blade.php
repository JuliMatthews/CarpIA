<div x-data="{ open: false }">
    {{-- Export button --}}
    <button
        @click="open = !open"
        class="p-2 text-[#888888] hover:text-[#f0f0f0] hover:bg-[#1e1e1e] rounded-lg transition-colors"
        title="Exportar conversación"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-56 bg-[#161616] border border-[#2a2a2a] rounded-xl shadow-xl z-50 py-2" style="display: none;">
        @if($conversation)
            <button wire:click="copyToClipboard" @click="open = false" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-[#f0f0f0] hover:bg-[#1e1e1e] transition-colors">
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Copiar conversación
            </button>
            <button wire:click="downloadMarkdown" @click="open = false" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-[#f0f0f0] hover:bg-[#1e1e1e] transition-colors">
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Markdown
            </button>
            <button wire:click="downloadTxt" @click="open = false" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-[#f0f0f0] hover:bg-[#1e1e1e] transition-colors">
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar TXT
            </button>
            <a href="{{ route('chat.export.pdf', $conversation->id) }}"
               target="_blank"
               @click="open = false"
               class="w-full flex items-center gap-3 px-4 py-2 text-sm text-[#f0f0f0] hover:bg-[#1e1e1e] transition-colors"
            >
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Descargar PDF
            </a>
        @else
            <div class="px-4 py-2 text-sm text-[#888888]">Inicia una conversación para exportar</div>
        @endif
    </div>
</div>
