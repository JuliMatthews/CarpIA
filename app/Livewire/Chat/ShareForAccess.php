<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ShareForAccess extends Component
{
    public bool $used = false;

    public function mount(): void
    {
        $this->used = auth()->user()->used_share_trial ?? false;
    }

    public function activateShare(string $network): void
    {
        $user = auth()->user();

        if ($user->used_share_trial) {
            $this->dispatch('flash', message: 'Ya usaste esta opción anteriormente.', type: 'error');
            return;
        }

        $user->update([
            'promo_access_until' => now()->addHours(24),
            'used_share_trial' => true,
        ]);

        $this->used = true;

        $this->dispatch('flash', message: '¡Genial! Tienes 24 horas de acceso gratis.', type: 'success');
        $this->dispatch('accessGranted');
    }

    public function getShareLinksProperty(): array
    {
        $text = urlencode('Estoy probando CarpIA.cl 🤖 ¡Chatea con IA gratis!');
        $url = urlencode('https://carpia.cl');

        return [
            'x' => "https://twitter.com/intent/tweet?text={$text}&url={$url}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}&quote={$text}",
            'whatsapp' => "https://wa.me/?text={$text}%20{$url}",
        ];
    }

    public function copyLink(): void
    {
        $this->dispatch('copyToClipboard', content: 'https://carpia.cl');
        $this->dispatch('flash', message: 'Link copiado al portapapeles.', type: 'success');
    }

    public function render()
    {
        return view('livewire.chat.share-for-access');
    }
}
