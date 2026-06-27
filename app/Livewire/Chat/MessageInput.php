<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class MessageInput extends Component
{
    public string $message = '';

    public function send(): void
    {
        $content = trim($this->message);

        if ($content === '') {
            return;
        }

        $this->dispatch('submitMessage', content: $content);
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chat.message-input');
    }
}
