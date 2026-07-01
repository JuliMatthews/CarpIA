<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;
use Illuminate\Support\Facades\Http;

class OllamaProvider extends AbstractAIProvider
{
    protected string $baseUrl;
    protected string $providerSlug = 'ollama';
    protected string $providerName = 'Ollama';
    protected int $defaultTimeout = 60;
    protected int $streamTimeout = 120;
    protected int $connectTimeout = 5;
    protected int $retries = 1;

    public function __construct()
    {
        $this->baseUrl = config('ai.providers.ollama.base_url', 'http://localhost:11434');
    }

    protected function getApiKey(): ?string
    {
        return null;
    }

    protected function buildHeaders(string $apiKey): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function buildPayload(array $messages, array $options): array
    {
        return [
            'model' => $options['model'] ?? 'llama3.2',
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2048,
        ];
    }

    protected function buildUrl(string $endpoint): string
    {
        return "{$this->baseUrl}/v1/chat/completions";
    }

    protected function parseResponse(array $data, string $model): AIResponseDTO
    {
        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'] ?? $model,
            provider: 'ollama',
            promptTokens: $data['usage']['prompt_tokens'] ?? null,
            completionTokens: $data['usage']['completion_tokens'] ?? null,
            totalTokens: $data['usage']['total_tokens'] ?? null,
            responseTimeMs: null,
        );
    }

    protected function parseStreamLine(string $line): ?string
    {
        $json = json_decode($line, true);

        if (isset($json['choices'][0]['delta']['content'])) {
            return $json['choices'][0]['delta']['content'];
        }

        return null;
    }

    protected function validateApiKey(?string $apiKey): void
    {
        // Ollama no necesita API key
    }

    public function getAvailableModels(): array
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/api/tags");

            if ($response->successful()) {
                $models = $response->json('models', []);

                return array_map(fn($model) => [
                    'id' => $model['name'],
                    'name' => $model['name'],
                    'context_window' => 128000,
                ], $models);
            }
        } catch (\Exception) {
            // Ollama not available
        }

        return [
            ['id' => 'llama3.2', 'name' => 'Llama 3.2', 'context_window' => 128000],
        ];
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }
}
