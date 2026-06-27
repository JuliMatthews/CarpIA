<?php

namespace App\Services;

use App\Models\PromptLibrary;
use App\Models\User;
use Illuminate\Support\Collection;

class PromptLibraryService
{
    public function getByUser(User $user): Collection
    {
        return PromptLibrary::where('user_id', $user->id)
            ->orderByDesc('use_count')
            ->get();
    }

    public function getPublic(): Collection
    {
        return PromptLibrary::where('is_public', true)
            ->orderByDesc('use_count')
            ->get();
    }

    public function getByCategory(User $user, string $category): Collection
    {
        return PromptLibrary::where('user_id', $user->id)
            ->where('category', $category)
            ->orderByDesc('use_count')
            ->get();
    }

    public function search(User $user, string $query): Collection
    {
        return PromptLibrary::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->orderByDesc('use_count')
            ->get();
    }

    public function create(User $user, array $data): PromptLibrary
    {
        return PromptLibrary::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'] ?? 'general',
            'is_public' => $data['is_public'] ?? false,
        ]);
    }

    public function update(PromptLibrary $prompt, array $data): PromptLibrary
    {
        $prompt->update($data);
        return $prompt->fresh();
    }

    public function delete(PromptLibrary $prompt): bool
    {
        return $prompt->delete();
    }

    public function incrementUseCount(PromptLibrary $prompt): PromptLibrary
    {
        $prompt->increment('use_count');
        return $prompt->fresh();
    }

    public function getCategories(): array
    {
        return [
            'general' => 'General',
            'redaccion' => 'Redacción',
            'codigo' => 'Código',
            'analisis' => 'Análisis',
            'creatividad' => 'Creatividad',
            'educacion' => 'Educación',
            'negocios' => 'Negocios',
            'traduccion' => 'Traducción',
        ];
    }
}
