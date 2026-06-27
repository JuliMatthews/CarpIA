<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

class ConversationService
{
    public function __construct(
        private MessageService $messageService,
    ) {}

    public function create(User $user, ?int $modelId = null, ?string $title = null): Conversation
    {
        return $user->conversations()->create([
            'model_id' => $modelId,
            'title' => $title,
            'temperature' => 0.7,
            'status' => 'active',
        ]);
    }

    public function getByUser(User $user): Collection
    {
        return $user->conversations()
            ->with('model.provider')
            ->latest()
            ->get();
    }

    public function getById(int $id, User $user): ?Conversation
    {
        return $user->conversations()
            ->with(['messages', 'model.provider'])
            ->find($id);
    }

    public function update(Conversation $conversation, array $data): Conversation
    {
        $conversation->update($data);
        return $conversation->fresh();
    }

    public function delete(Conversation $conversation): bool
    {
        return $conversation->delete();
    }

    public function updateTitle(Conversation $conversation, string $title): Conversation
    {
        return $this->update($conversation, ['title' => $title]);
    }

    public function updateModel(Conversation $conversation, int $modelId): Conversation
    {
        return $this->update($conversation, ['model_id' => $modelId]);
    }

    public function addTokens(Conversation $conversation, int $tokens): Conversation
    {
        $conversation->increment('total_tokens', $tokens);
        return $conversation->fresh();
    }
}
