<?php

namespace App\Livewire\Settings;

use App\Models\AiModel;
use App\Models\UserSetting;
use Livewire\Component;

class UserSettings extends Component
{
    public string $theme = 'dark';
    public ?int $defaultModelId = null;
    public float $temperature = 0.7;
    public int $maxTokens = 2048;
    public string $systemPrompt = '';
    public array $models = [];

    public function mount(): void
    {
        $user = auth()->user();
        $settings = $user->settings;

        if ($settings) {
            $this->theme = $settings->theme ?? 'dark';
            $this->defaultModelId = $settings->default_model_id;
            $this->temperature = $settings->temperature ?? 0.7;
            $this->maxTokens = $settings->max_tokens ?? 2048;
            $this->systemPrompt = $settings->system_prompt ?? '';
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
            'temperature' => 'required|numeric|min:0|max:2',
            'maxTokens' => 'required|integer|min:100|max:8192',
            'systemPrompt' => 'nullable|string|max:2000',
        ]);

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $this->theme,
                'default_model_id' => $this->defaultModelId,
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'system_prompt' => $this->systemPrompt,
            ]
        );

        // Update user default model
        $user->update(['default_model_id' => $this->defaultModelId]);

        session()->flash('settings-saved', true);

        $this->dispatch('theme-changed', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.settings.user-settings');
    }
}
