<?php

namespace App\Livewire\Sidebar;

use App\Models\PromptLibrary as PromptLibraryModel;
use App\Services\PromptLibraryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PromptLibrary extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $content = '';
    public string $category = 'general';
    public bool $isPublic = false;
    public string $search = '';
    public array $prompts = [];
    public string $selectedCategory = 'all';

    public function mount(PromptLibraryService $service): void
    {
        $this->loadPrompts($service);
    }

    #[On('promptCreated')]
    #[On('promptDeleted')]
    public function loadPrompts(?PromptLibraryService $service = null): void
    {
        $service = $service ?? app(PromptLibraryService::class);
        $user = Auth::user();

        if ($this->search !== '') {
            $this->prompts = $service->search($user, $this->search)->toArray();
        } elseif ($this->selectedCategory !== 'all') {
            $this->prompts = $service->getByCategory($user, $this->selectedCategory)->toArray();
        } else {
            $this->prompts = $service->getByUser($user)->toArray();
        }
    }

    public function updatedSearch(): void
    {
        $this->loadPrompts();
    }

    public function updatedSelectedCategory(): void
    {
        $this->loadPrompts();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $id): void
    {
        $prompt = PromptLibraryModel::findOrFail($id);
        $this->editingId = $id;
        $this->title = $prompt->title;
        $this->content = $prompt->content;
        $this->category = $prompt->category;
        $this->isPublic = $prompt->is_public;
        $this->showModal = true;
        $this->isEditing = true;
    }

    public function save(PromptLibraryService $service): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
        ]);

        $user = Auth::user();

        if ($this->isEditing && $this->editingId) {
            $prompt = PromptLibraryModel::findOrFail($this->editingId);
            $service->update($prompt, [
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'is_public' => $this->isPublic,
            ]);
        } else {
            $service->create($user, [
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'is_public' => $this->isPublic,
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->loadPrompts($service);
    }

    public function delete(int $id, PromptLibraryService $service): void
    {
        $prompt = PromptLibraryModel::findOrFail($id);
        $service->delete($prompt);
        $this->loadPrompts($service);
    }

    public function usePrompt(int $id, PromptLibraryService $service): void
    {
        $prompt = PromptLibraryModel::findOrFail($id);
        $service->incrementUseCount($prompt);
        $this->dispatch('insertPrompt', content: $prompt->content);
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->content = '';
        $this->category = 'general';
        $this->isPublic = false;
        $this->editingId = null;
    }

    public function render()
    {
        $categories = app(PromptLibraryService::class)->getCategories();

        return view('livewire.sidebar.prompt-library', [
            'categories' => $categories,
        ]);
    }
}
