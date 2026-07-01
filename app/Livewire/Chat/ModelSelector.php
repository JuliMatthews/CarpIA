<?php

namespace App\Livewire\Chat;

use App\AI\AIManager;
use App\Models\AiModel;
use Livewire\Component;

class ModelSelector extends Component
{
    public ?int $selectedModelId = null;
    public bool $isOpen = false;
    public array $providers = [];

    public function mount(?int $modelId = null): void
    {
        $this->selectedModelId = $modelId;
        $this->loadProviders();
    }

    public function loadProviders(): void
    {
        $manager = app(AIManager::class);
        $activeProviders = $manager->getActiveProviders();

        $excludedProviders = ['openrouter', 'gemini', 'deepseek'];

        $priorityOrder = ['groq', 'mistral', 'cloudflare', 'huggingface', 'ollama'];

        $sortedProviders = [];
        foreach ($priorityOrder as $slug) {
            if (isset($activeProviders[$slug]) && !in_array($slug, $excludedProviders)) {
                $sortedProviders[$slug] = $activeProviders[$slug];
            }
        }
        foreach ($activeProviders as $slug => $provider) {
            if (!isset($sortedProviders[$slug]) && !in_array($slug, $excludedProviders)) {
                $sortedProviders[$slug] = $provider;
            }
        }

        $this->providers = [];

        foreach ($sortedProviders as $slug => $provider) {
            $models = AiModel::where('is_active', true)
                ->whereHas('provider', fn($q) => $q->where('slug', $slug))
                ->with('provider')
                ->get();

            if ($models->isNotEmpty()) {
                $this->providers[] = [
                    'name' => $provider->getName(),
                    'slug' => $slug,
                    'models' => $models->map(fn($model) => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'slug' => $model->slug,
                        'is_free' => $model->is_free,
                        'context_window' => $model->context_window,
                    ])->toArray(),
                ];
            }
        }
    }

    public function selectModel(int $modelId): void
    {
        $this->selectedModelId = $modelId;
        $this->isOpen = false;

        $this->dispatch('modelSelected', modelId: $modelId);
    }

    public function getSelectedModelName(): string
    {
        if (!$this->selectedModelId) {
            return 'Seleccionar modelo';
        }

        $model = AiModel::with('provider')->find($this->selectedModelId);

        return $model ? "{$model->provider->name} / {$model->name}" : 'Seleccionar modelo';
    }

    public function render()
    {
        return view('livewire.chat.model-selector');
    }
}
