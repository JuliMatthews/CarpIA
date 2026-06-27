<?php

namespace App\Livewire\Settings;

use App\Models\AiModel;
use App\Models\UserSetting;
use Livewire\Component;

class UserSettings extends Component
{
    public string $theme = 'dark';
    public string $language = 'es';
    public ?int $defaultModelId = null;
    public float $temperature = 0.7;
    public int $maxTokens = 2048;
    public array $models = [];

    public function mount(): void
    {
        $user = auth()->user();
        $settings = $user->settings;

        if ($settings) {
            $this->theme = $settings->theme ?? 'dark';
            $this->language = $settings->language ?? 'es';
            $this->defaultModelId = $settings->default_model_id;
            $this->temperature = $settings->temperature ?? 0.7;
            $this->maxTokens = $settings->max_tokens ?? 2048;
        }

        $this->models = AiModel::where('is_active', true)
            ->with('provider')
            ->get()
            ->map(fn($model) => [
                'id' => $model->id,
                'name' => "{$model->provider->name} / {$model->name}",
            ])
            ->toArray();
    }

    public function save(): void
    {
        $user = auth()->user();

        $this->validate([
            'theme' => 'required|in:dark,light',
            'language' => 'required|in:es,en',
            'temperature' => 'required|numeric|min:0|max:2',
            'maxTokens' => 'required|integer|min:100|max:8192',
        ]);

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $this->theme,
                'language' => $this->language,
                'default_model_id' => $this->defaultModelId,
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
            ]
        );

        // Update user default model
        $user->update(['default_model_id' => $this->defaultModelId]);

        session()->flash('settings-saved', true);
    }

    public function render()
    {
        return view('livewire.settings.user-settings');
    }
}
