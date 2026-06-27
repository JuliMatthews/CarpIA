<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

class SearchService
{
    public function searchConversations(User $user, string $query): Collection
    {
        return Conversation::where('user_id', $user->id)
            ->where('title', 'like', "%{$query}%")
            ->with('model.provider')
            ->latest()
            ->get();
    }

    public function searchMessages(User $user, string $query): Collection
    {
        return Conversation::where('user_id', $user->id)
            ->whereHas('messages', function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%");
            })
            ->with('model.provider')
            ->latest()
            ->get();
    }

    public function searchAll(User $user, string $query): array
    {
        return [
            'conversations' => $this->searchConversations($user, $query),
            'prompts' => app(PromptLibraryService::class)->search($user, $query),
        ];
    }
}
