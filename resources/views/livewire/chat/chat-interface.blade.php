<div
    x-data="{ messageSent: false }"
    @message-sent.window="messageSent = !messageSent"
    @copy-to-clipboard.window="
        navigator.clipboard.writeText($event.detail.content);
        showToast('Copiado al portapapeles');
    "
    @download-file.window="
        const data = $event.detail;
        const binary = atob(data.content);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        const blob = new Blob([bytes], { type: data.mime });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = data.filename;
        a.click();
        URL.revokeObjectURL(url);
    "
    x-init="
        window.showToast = (msg) => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 px-4 py-2 bg-[#7c3aed] text-white text-sm rounded-lg shadow-lg z-50';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    "
    class="flex flex-col h-full"
>
    {{-- Header de la conversación --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-[#2a2a2a]">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-medium text-[#f0f0f0]">
                {{ $conversation?->title ?: 'Nueva conversación' }}
            </h2>
            <livewire:chat.model-selector :modelId="$selectedModelId" :key="'model-selector-' . ($conversation?->id ?? 'new')" />
        </div>
        <div class="flex items-center gap-2">
            <livewire:chat.export-chat :conversation="$conversation" :key="'export-' . ($conversation?->id ?? 'new')" />
            <button
                wire:click="newConversation"
                class="px-4 py-2 text-sm bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#888888] hover:text-[#f0f0f0] hover:border-[#7c3aed] transition-colors"
            >
                Nueva conversación
            </button>
        </div>
    </div>

    {{-- Área de mensajes --}}
    <div class="flex-1 overflow-y-auto px-6 py-4">
        @if(empty($messages))
            {{-- Estado vacío --}}
            <div class="flex flex-col items-center justify-center h-full text-center">
                <div class="mb-4">
                    <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-20 h-20 mx-auto" />
                </div>
                <h3 class="text-xl font-medium text-[#f0f0f0] mb-2">¡Hola! Soy CarpIA</h3>
                <p class="text-[#888888] max-w-md">
                    Tu asistente de IA unificado. Pregúntame lo que quieras, puedo usar diferentes modelos de inteligencia artificial.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-4 max-w-lg">
                    <button
                        wire:click="$dispatch('submitMessage', { content: '¿Cómo preparar la tierra para sembrar verduras en casa?' })"
                        class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                    >
                        <div class="text-sm text-[#f0f0f0] font-medium">Preparar tierra para verduras</div>
                        <div class="text-xs text-[#888888] mt-1">Huerto casero</div>
                    </button>
                    <button
                        wire:click="$dispatch('submitMessage', { content: '¿Cuáles son las mejores plantas para principiantes en jardinería?' })"
                        class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                    >
                        <div class="text-sm text-[#f0f0f0] font-medium">Mejores plantas para empezar</div>
                        <div class="text-xs text-[#888888] mt-1">Jardinería básica</div>
                    </button>
                    <button
                        wire:click="$dispatch('submitMessage', { content: '¿Cómo cuidar plantas de interior durante el invierno?' })"
                        class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                    >
                        <div class="text-sm text-[#f0f0f0] font-medium">Cuidar plantas en invierno</div>
                        <div class="text-xs text-[#888888] mt-1">Plantas de interior</div>
                    </button>
                    <button
                        wire:click="$dispatch('submitMessage', { content: '¿Qué semillas puedo sembrar en primavera en Chile?' })"
                        class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors"
                    >
                        <div class="text-sm text-[#f0f0f0] font-medium">Semillas de primavera en Chile</div>
                        <div class="text-xs text-[#888888] mt-1">Siembra estacional</div>
                    </button>
                </div>
            </div>
        @else
            {{-- Lista de mensajes --}}
            <div class="space-y-6">
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] {{ $msg['role'] === 'user'
                            ? 'bg-[#7c3aed] text-white rounded-2xl rounded-tr-sm'
                            : 'bg-[#1e1e1e] border border-[#2a2a2a] text-[#f0f0f0] rounded-2xl rounded-tl-sm'
                        }} px-4 py-3">
                            @if($msg['role'] === 'assistant')
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm">🐾</span>
                                    <span class="text-xs text-[#888888]">CarpIA</span>
                                    @if(isset($msg['metadata']['model']))
                                        <span class="px-1.5 py-0.5 text-[10px] bg-[#7c3aed]/20 text-[#a78bfa] rounded">
                                            {{ $msg['metadata']['model'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <div class="text-sm whitespace-pre-wrap">{{ $msg['content'] }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- Streaming de respuesta --}}
                @if($isLoading && !empty($streamedContent))
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-[#1e1e1e] border border-[#2a2a2a] text-[#f0f0f0] rounded-2xl rounded-tl-sm px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm">🐾</span>
                                <span class="text-xs text-[#888888]">CarpIA</span>
                                <span class="px-1.5 py-0.5 text-[10px] bg-[#7c3aed]/20 text-[#a78bfa] rounded">
                                    escribiendo...
                                </span>
                            </div>
                            <div class="text-sm whitespace-pre-wrap">{{ $streamedContent }}</div>
                        </div>
                    </div>
                @endif

                {{-- Indicador de carga --}}
                @if($isLoading && empty($streamedContent))
                    <div class="flex justify-start">
                        <div class="bg-[#1e1e1e] border border-[#2a2a2a] rounded-2xl rounded-tl-sm px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm">🐾</span>
                                <span class="text-xs text-[#888888]">CarpIA</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-[#7c3aed] rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-2 h-2 bg-[#7c3aed] rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-2 h-2 bg-[#7c3aed] rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Input del mensaje --}}
    <div class="border-t border-[#2a2a2a] px-6 py-4 bg-[#0d0d0d]">
        <livewire:chat.message-input />
    </div>
</div>
