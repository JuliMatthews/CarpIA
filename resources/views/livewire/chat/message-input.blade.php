<div>
    <form wire:submit.prevent="send" class="flex items-end gap-4">
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
