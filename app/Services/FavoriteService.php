<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Support\Collection;

class FavoriteService
{
    public function toggle(User $user, int $conversationId): bool
    {
        $existing = Favorite::where('user_id', $user->id)
            ->where('conversation_id', $conversationId)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        Favorite::create([
            'user_id' => $user->id,
            'conversation_id' => $conversationId,
        ]);

        return true;
    }

    public function isFavorite(User $user, int $conversationId): bool
    {
        return Favorite::where('user_id', $user->id)
            ->where('conversation_id', $conversationId)
            ->exists();
    }

    public function getFavorites(User $user): Collection
    {
        return Favorite::where('user_id', $user->id)
            ->with(['conversation.model.provider', 'conversation.messages'])
            ->latest()
            ->get()
            ->pluck('conversation');
    }
}
