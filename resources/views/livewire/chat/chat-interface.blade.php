<div
    x-data="{ messageSent: false }"
    @message-sent.window="
        messageSent = !messageSent;
        $nextTick(() => {
            const el = $el.querySelector('.messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        });
    "
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
        <div class="max-w-3xl mx-auto flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-medium text-[#f0f0f0]">
                    {{ $conversation?->title ?: 'Nueva conversación' }}
                </h2>
                <livewire:chat.model-selector :modelId="$selectedModelId" :key="'model-selector-' . ($conversation?->id?? 'new')" />
            </div>
            <div class="flex items-center gap-2">
                <button
                    wire:click="$toggle('searchWeb')"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg border transition-colors
                        {{ $searchWeb
                            ? 'bg-[#7c3aed]/20 border-[#7c3aed] text-[#a78bfa]'
                            : 'bg-[#1e1e1e] border-[#2a2a2a] text-[#888888] hover:text-[#f0f0f0]'
                        }}"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Web
                </button>
                <livewire:chat.export-chat :conversation="$conversation" :key="'export-' . ($conversation?->id ?? 'new')" />
                <button
                    wire:click="newConversation"
                    class="px-4 py-2 text-sm bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#888888] hover:text-[#f0f0f0] hover:border-[#7c3aed] transition-colors"
                >
                    Nueva conversación
                </button>
            </div>
        </div>
    </div>

    {{-- Área de mensajes --}}
    <div class="flex-1 overflow-y-auto px-6 py-4 messages-container">
        @php
            $user = auth()->user();
            $hasAccess = $user->is_admin ||
                ($user->promo_access_until && $user->promo_access_until > now()) ||
                \App\Models\Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('ends_at', '>', now())
                    ->exists();
        @endphp

        @if(!$hasAccess)
            {{-- Panel de suscripción --}}
            <div class="flex flex-col items-center justify-center h-full text-center">
                <div class="w-full max-w-md bg-[#161616] border border-[#2a2a2a] rounded-2xl p-8 flex flex-col items-center">
                    <img src="{{ asset('pet-2.png') }}" alt="CarpIA" class="w-20 h-20 object-contain mb-4">
                    <h3 class="text-xl font-bold text-[#f0f0f0] mb-2">Activa tu cuenta</h3>
                    <p class="text-sm text-[#888888] mb-6">Para usar CarpIA necesitas una suscripción activa.</p>

                    <div class="w-full bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl p-5 mb-6">
                        <div class="text-3xl font-bold text-[#7c3aed] mb-1">$1.990</div>
                        <div class="text-xs text-[#888888]">CLP / mes · Acceso completo a todos los modelos</div>
                        <a href="/checkout/direct" style="display:inline-block;margin-top:1rem;padding:0.75rem 2rem;background-color:#10b981;color:white;border-radius:0.75rem;font-size:0.875rem;font-weight:600;text-align:center;text-decoration:none;">
                            Obtener Suscripción
                        </a>
                    </div>

                    <div class="relative w-full my-2 mb-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[#2a2a2a]"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-[#161616] text-[#888888]">o ingresa un código de acceso</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="w-full mb-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg text-sm text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="w-full mb-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-sm text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('promo.redeem') }}" class="w-full">
                        @csrf
                        <div class="flex gap-2">
                            <input
                                type="text"
                                name="code"
                                placeholder="CÓDIGO-PROMO"
                                class="flex-1 px-4 py-3 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-[#f0f0f0] text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] outline-none uppercase"
                                required
                            >
                            <button type="submit" class="px-6 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-lg text-sm font-medium transition-colors">
                                Activar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif(empty($messages))
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
                    <button wire:click="$dispatch('submitMessage', { content: '¿Cómo preparar la tierra para sembrar verduras en casa?' })" class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors">
                        <div class="text-sm text-[#f0f0f0] font-medium">Preparar tierra para verduras</div>
                        <div class="text-xs text-[#888888] mt-1">Huerto casero</div>
                    </button>
                    <button wire:click="$dispatch('submitMessage', { content: '¿Cuáles son las mejores plantas para principiantes en jardinería?' })" class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors">
                        <div class="text-sm text-[#f0f0f0] font-medium">Mejores plantas para empezar</div>
                        <div class="text-xs text-[#888888] mt-1">Jardinería básica</div>
                    </button>
                    <button wire:click="$dispatch('submitMessage', { content: '¿Cómo cuidar plantas de interior durante el invierno?' })" class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors">
                        <div class="text-sm text-[#f0f0f0] font-medium">Cuidar plantas en invierno</div>
                        <div class="text-xs text-[#888888] mt-1">Plantas de interior</div>
                    </button>
                    <button wire:click="$dispatch('submitMessage', { content: '¿Qué semillas puedo sembrar en primavera en Chile?' })" class="p-4 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-left hover:border-[#7c3aed] transition-colors">
                        <div class="text-sm text-[#f0f0f0] font-medium">Semillas de primavera en Chile</div>
                        <div class="text-xs text-[#888888] mt-1">Siembra estacional</div>
                    </button>
                </div>
            </div>
        @else
            {{-- Lista de mensajes --}}
            <div class="max-w-3xl mx-auto space-y-6">
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

                @if($isLoading && !empty($streamedContent))
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-[#1e1e1e] border border-[#2a2a2a] text-[#f0f0f0] rounded-2xl rounded-tl-sm px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm">🐾</span>
                                <span class="text-xs text-[#888888]">CarpIA</span>
                                <span class="px-1.5 py-0.5 text-[10px] bg-[#7c3aed]/20 text-[#a78bfa] rounded">escribiendo...</span>
                            </div>
                            <div class="text-sm whitespace-pre-wrap">{{ $streamedContent }}</div>
                        </div>
                    </div>
                @endif

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
    @if($hasAccess ?? false)
    <div class="border-t border-[#2a2a2a] px-6 py-4 bg-[#0d0d0d]">
        <div class="max-w-3xl mx-auto">
            <livewire:chat.message-input />
        </div>
    </div>
    @else
    <div class="border-t border-[#2a2a2a] px-6 py-4 bg-[#0d0d0d]">
        <div class="max-w-3xl mx-auto">
            <div class="w-full px-4 py-3 bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl text-[#888888] text-sm text-center cursor-not-allowed">
                🔒 Activa tu suscripción para comenzar a chatear
            </div>
        </div>
    </div>
    @endif
</div>
