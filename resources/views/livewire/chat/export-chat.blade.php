<div x-data="{ open: false }" @open-export-modal.window="open = true">
    {{-- Export button --}}
    @if($conversation)
        <button
            @click="open = true"
            class="p-2 text-[#888888] hover:text-[#f0f0f0] hover:bg-[#1e1e1e] rounded-lg transition-colors"
            title="Exportar conversación"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </button>
    @endif

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/75" @click="open = false"></div>
            <div class="relative bg-[#161616] border border-[#2a2a2a] rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-[#f0f0f0] mb-4">Exportar conversación</h3>

                    <div class="space-y-3">
                        <button
                            wire:click="copyToClipboard"
                            @click="open = false"
                            class="w-full flex items-center gap-3 p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-[#f0f0f0]">Copiar al portapapeles</div>
                                <div class="text-xs text-[#888888]">Copia el contenido en formato Markdown</div>
                            </div>
                        </button>

                        <button
                            wire:click="downloadMarkdown"
                            @click="open = false"
                            class="w-full flex items-center gap-3 p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-[#f0f0f0]">Descargar Markdown</div>
                                <div class="text-xs text-[#888888]">Guarda el archivo .md en tu computadora</div>
                            </div>
                        </button>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            @click="open = false"
                            class="px-4 py-2 text-sm text-[#888888] hover:text-[#f0f0f0] transition-colors"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
