<?php

namespace App\AI;

use App\AI\Contracts\AIProvider;
use App\AI\Providers\CloudflareProvider;
use App\AI\Providers\DeepSeekProvider;
use App\AI\Providers\GeminiProvider;
use App\AI\Providers\GroqProvider;
use App\AI\Providers\HuggingFaceProvider;
use App\AI\Providers\MistralProvider;
use App\AI\Providers\OllamaProvider;
use App\AI\Providers\OpenRouterProvider;
use InvalidArgumentException;

class AIManager
{
    private array $providers = [];

    public function __construct(
        GroqProvider $groq,
        GeminiProvider $gemini,
        OpenRouterProvider $openRouter,
        CloudflareProvider $cloudflare,
        HuggingFaceProvider $huggingface,
        MistralProvider $mistral,
        DeepSeekProvider $deepseek,
        OllamaProvider $ollama,
    ) {
        $this->providers = [
            'groq' => $groq,
            'gemini' => $gemini,
            'openrouter' => $openRouter,
            'cloudflare' => $cloudflare,
            'huggingface' => $huggingface,
            'mistral' => $mistral,
            'deepseek' => $deepseek,
            'ollama' => $ollama,
        ];
    }

    public function provider(string $slug): AIProvider
    {
        if (!isset($this->providers[$slug])) {
            throw new InvalidArgumentException("Proveedor '{$slug}' no encontrado.");
        }

        return $this->providers[$slug];
    }

    public function getActiveProviders(): array
    {
        return array_filter(
            $this->providers,
            fn(AIProvider $provider) => $provider->isAvailable()
        );
    }

    public function getAllProviders(): array
    {
        return $this->providers;
    }

    public function getProviderForModel(string $modelId): ?AIProvider
    {
        foreach ($this->providers as $provider) {
            $models = array_column($provider->getAvailableModels(), 'id');
            if (in_array($modelId, $models)) {
                return $provider;
            }
        }

        return null;
    }
}
