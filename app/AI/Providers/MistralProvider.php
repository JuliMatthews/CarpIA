<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;

class MistralProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://api.mistral.ai/v1';
    protected string $providerSlug = 'mistral';
    protected string $providerName = 'Mistral';
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.mistral.api_key');
    }

    protected function buildHeaders(string $apiKey): array
    {
        return [
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'CarpIA/1.0',
        ];
    }

    protected function buildPayload(array $messages, array $options): array
    {
        return [
            'model' => $options['model'] ?? 'mistral-small-latest',
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2048,
        ];
    }

    protected function parseResponse(array $data, string $model): AIResponseDTO
    {
        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'] ?? $model,
            provider: 'mistral',
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

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'mistral-small-latest', 'name' => 'Mistral Small', 'context_window' => 32768],
            ['id' => 'open-mistral-7b', 'name' => 'Mistral 7B', 'context_window' => 32768],
            ['id' => 'open-mixtral-8x7b', 'name' => 'Mixtral 8x7B', 'context_window' => 32768],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.mistral.api_key'));
    }
}
