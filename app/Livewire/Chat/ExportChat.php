<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
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
        $filename = 'carpia-' . Str::slug($title) . '.md';

        $this->dispatch('downloadFile', [
            'filename' => $filename,
            'content' => base64_encode($markdown),
            'mime' => 'text/markdown',
        ]);
    }

    public function downloadTxt(): void
    {
        $markdown = $this->exportMarkdown();
        $title = $this->conversation->title ?? 'chat';
        $filename = 'carpia-' . slug($title) . '.txt';

        $this->dispatch('downloadFile', [
            'filename' => $filename,
            'content' => base64_encode($markdown),
            'mime' => 'text/plain',
        ]);
    }

    public function downloadPdf()
    {
        if (!$this->conversation) {
            return;
        }

        $conversation = $this->conversation;
        $modelName = $conversation->model?->name ?? 'Desconocido';

        $html = view('exports.conversation-pdf', compact('conversation', 'modelName'))->render();

        $pdf = Pdf::loadHTML($html);
        $title = $conversation->title ?? 'chat';
        $filename = 'carpia-' . Str::slug($title) . '.pdf';

        $this->dispatch('downloadFile', [
            'filename' => $filename,
            'content' => base64_encode($pdf->output()),
            'mime' => 'application/pdf',
        ]);
    }

    public function getShareUrl(): ?string
    {
        if (!$this->conversation) {
            return null;
        }

        // TODO: implementar share links con tokens únicos
        return null;
    }

    public function render()
    {
        return view('livewire.chat.export-chat');
    }
}
