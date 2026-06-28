<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;

class MessageInput extends Component
{
    use WithFileUploads;

    public string $message = '';
    public $files = [];
    public array $uploadedFiles = [];
    public bool $showFilePicker = false;

    protected $rules = [
        'files.*' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,txt,csv,doc,docx,xls,xlsx,mp3,mp4',
    ];

    public function send(): void
    {
        $content = trim($this->message);

        if ($content === '' && empty($this->uploadedFiles)) {
            return;
        }

        $fileMeta = $this->uploadedFiles;

        $this->dispatch('submitMessage', content: $content, files: $fileMeta);
        $this->message = '';
        $this->uploadedFiles = [];
        $this->files = [];
        $this->showFilePicker = false;
    }

    public function updatedFiles(): void
    {
        $this->validate();

        foreach ($this->files as $file) {
            $path = $file->store('chat-uploads', 'public');

            $type = 'file';
            if (str_starts_with($file->getMimeType(), 'image/')) {
                $type = 'image';
            } elseif ($file->getMimeType() === 'application/pdf') {
                $type = 'pdf';
            }

            $this->uploadedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $type,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        $this->files = [];
    }

    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    public function render()
    {
        return view('livewire.chat.message-input');
    }
}
