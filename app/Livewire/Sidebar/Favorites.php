<?php

namespace App\Livewire\Sidebar;

use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Favorites extends Component
{
    public array $favorites = [];

    #[On('favoriteToggled')]
    public function loadFavoritesEvent(): void
    {
        $this->loadFavorites();
    }

    public function mount(FavoriteService $service): void
    {
        $this->loadFavorites($service);
    }

    public function loadFavorites(?FavoriteService $service = null): void
    {
        $service = $service ?? app(FavoriteService::class);
        $user = Auth::user();
        $this->favorites = $service->getFavorites($user)->toArray();
    }

    public function render()
    {
        return view('livewire.sidebar.favorites');
    }
}
