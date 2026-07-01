<?php

namespace App\Livewire\Sidebar;

use App\Models\Favorite;
use App\Services\ConversationService;
use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConversationList extends Component
{
    public string $search = '';
    public $conversations;

    public function getListeners(): array
    {
        return [
            'conversationCreated' => 'loadConversations',
            'conversationDeleted' => 'loadConversations',
            'favoriteToggled' => 'loadConversations',
        ];
    }

    public function mount(ConversationService $conversationService): void
    {
        $this->loadConversations($conversationService);
    }

    public function loadConversations(?ConversationService $conversationService = null): void
    {
        $conversationService = $conversationService ?? app(ConversationService::class);
        $user = Auth::user();

        if ($this->search !== '') {
            $this->conversations = $user->conversations()
                ->with('model.provider')
                ->where('title', 'like', "%{$this->search}%")
                ->latest()
                ->get();
        } else {
            $this->conversations = $conversationService->getByUser($user);
        }
    }

    public function updatedSearch(): void
    {
        $this->loadConversations();
    }

    public function toggleFavorite(int $conversationId): void
    {
        $user = Auth::user();
        app(FavoriteService::class)->toggle($user, $conversationId);
        $this->dispatch('favoriteToggled');
    }

    public function isFavorite(int $conversationId): bool
    {
        return app(FavoriteService::class)->isFavorite(Auth::user(), $conversationId);
    }

    public function deleteConversation(int $conversationId): void
    {
        $user = Auth::user();
        $conversation = $user->conversations()->find($conversationId);

        if ($conversation) {
            app(ConversationService::class)->delete($conversation);
            $this->dispatch('conversationDeleted');
        }
    }

    public function render()
    {
        return view('livewire.sidebar.conversation-list');
    }
}
