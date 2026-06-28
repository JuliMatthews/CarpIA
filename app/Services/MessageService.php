<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Collection;

class MessageService
{
    public function getByConversation(Conversation $conversation): Collection
    {
        return $conversation->messages()
            ->orderBy('created_at')
            ->get();
    }

    public function store(Conversation $conversation, string $role, string $content, ?array $metadata = null): Message
    {
        return $conversation->messages()->create([
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
        ]);
    }

    public function storeUserMessage(Conversation $conversation, string $content, ?array $metadata = null): Message
    {
        return $this->store($conversation, 'user', $content, $metadata);
    }

    public function storeAssistantMessage(Conversation $conversation, string $content, ?int $tokens = null, ?int $responseTimeMs = null, ?string $model = null): Message
    {
        $metadata = [];
        if ($model) {
            $metadata['model'] = $model;
        }

        return $this->store($conversation, 'assistant', $content, $metadata);
    }

    public function formatForAI(Conversation $conversation): array
    {
        $messages = $this->getByConversation($conversation);

        $formatted = [];
        foreach ($messages as $message) {
            $formatted[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        return $formatted;
    }
}
