<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;

class ExportChat extends Component
{
    public ?Conversation $conversation = null;
    public bool $showModal = false;

    public function mount(?Conversation $conversation = null): void
    {
        $this->conversation = $conversation;
    }

    public function exportMarkdown(): string
    {
        if (!$this->conversation) {
            return '';
        }

        $lines = [];
        $lines[] = "# {$this->conversation->title}";
        $lines[] = '';
        $lines[] = "Fecha: {$this->conversation->created_at->format('d/m/Y H:i')}";
        $modelName = $this->conversation->model?->name ?? 'Desconocido';
        $lines[] = "Modelo: {$modelName}";
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        foreach ($this->conversation->messages as $message) {
            $role = $message->role === 'user' ? '**Tú**' : '**CarpIA**';
            $lines[] = "{$role}:";
            $lines[] = $message->content;
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    public function copyToClipboard(): void
    {
        $markdown = $this->exportMarkdown();
        $this->dispatch('copyToClipboard', content: $markdown);
    }

    public function downloadMarkdown(): void
    {
        $markdown = $this->exportMarkdown();
        $title = $this->conversation->title ?? 'chat';
        $filename = 'carpia-' . slug($title) . '.md';

        $this->dispatch('downloadFile', [
            'filename' => $filename,
            'content' => base64_encode($markdown),
            'mime' => 'text/markdown',
        ]);
    }

    public function render()
    {
        return view('livewire.chat.export-chat');
    }
}
