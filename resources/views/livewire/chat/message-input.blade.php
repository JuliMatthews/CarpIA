<div>
    {{-- File preview --}}
    @if(!empty($uploadedFiles))
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($uploadedFiles as $i => $file)
                <div class="flex items-center gap-2 px-3 py-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg text-sm">
                    <span>{{ $file['type'] === 'image' ? '🖼️' : ($file['type'] === 'pdf' ? '📄' : '📎') }}</span>
                    <span class="text-[#f0f0f0] truncate max-w-[150px]">{{ $file['name'] }}</span>
                    <button wire:click="removeFile({{ $i }})" class="text-[#888888] hover:text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <form wire:submit.prevent="send" class="flex items-end gap-3">
        {{-- File picker button --}}
        <div class="relative">
            <button
                type="button"
                wire:click="$toggle('showFilePicker')"
                class="p-3 bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl text-[#888888] hover:text-[#a78bfa] hover:border-[#7c3aed] transition-colors"
                title="Adjuntar archivo"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </button>

            @if($showFilePicker)
                <div class="absolute bottom-full left-0 mb-2 p-4 bg-[#161616] border border-[#2a2a2a] rounded-xl shadow-xl z-50 w-72">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-[#f0f0f0]">Adjuntar archivos</span>
                        <button wire:click="$set('showFilePicker', false)" class="text-[#888888] hover:text-[#f0f0f0]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <input
                        type="file"
                        wire:model="files"
                        multiple
                        accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.mp3,.mp4"
                        class="block w-full text-sm text-[#888888] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#7c3aed] file:text-white hover:file:bg-[#6d28d9] file:cursor-pointer"
                    >
                    <p class="text-xs text-[#555555] mt-2">Imágenes, PDF, TXT, CSV, Office. Máx 20MB por archivo.</p>
                    <div wire:loading wire:target="files" class="text-sm text-[#a78bfa] mt-2">Subiendo archivo...</div>
                </div>
            @endif
        </div>

        {{-- Mic button --}}
        <button
            type="button"
            x-data="{ listening: false, recognition: null }"
            x-init="
                if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    recognition = new SpeechRecognition();
                    recognition.lang = 'es-CL';
                    recognition.continuous = false;
                    recognition.interimResults = true;

                    recognition.onresult = (e) => {
                        let transcript = '';
                        for (let i = e.resultIndex; i < e.results.length; i++) {
                            transcript += e.results[i][0].transcript;
                        }
                        $wire.$set('message', $wire.message + ' ' + transcript);
                    };

                    recognition.onend = () => { listening = false; };
                }
            "
            @click="
                if (!recognition) { alert('Voz no soportada en este navegador'); return; }
                if (listening) { recognition.stop(); listening = false; }
                else { recognition.start(); listening = true; }
            "
            :class="listening ? 'bg-red-500 border-red-500 text-white' : 'bg-[#1e1e1e] border-[#2a2a2a] text-[#888888] hover:text-[#a78bfa]'"
            class="p-3 border rounded-xl transition-colors"
            title="Dictado por voz"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
        </button>

        {{-- Message textarea --}}
        <div class="flex-1 relative">
            <textarea
                wire:model.live="message"
                id="chat-textarea"
                placeholder="Escribe tu mensaje... (Enter para enviar, Shift+Enter para nueva línea)"
                rows="1"
                class="w-full px-4 py-3 pr-12 bg-[#1e1e1e] border border-[#2a2a2a] rounded-xl text-[#f0f0f0] placeholder-[#888888] focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] focus:outline-none resize-none"
            ></textarea>
        </div>
        <button
            type="submit"
            id="send-btn"
            wire:loading.attr="disabled"
            class="px-4 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white transition-colors flex items-center justify-center"
        >
            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </form>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    const textarea = document.getElementById('chat-textarea');
    const sendBtn = document.getElementById('send-btn');
    if (textarea && sendBtn) {
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    }
});
</script>
