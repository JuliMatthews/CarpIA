<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;

class OpenRouterProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected string $providerSlug = 'openrouter';
    protected string $providerName = 'OpenRouter';
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.openrouter.api_key');
    }

    protected function buildHeaders(string $apiKey): array
    {
        return [
            'Authorization' => "Bearer {$apiKey}",
            'HTTP-Referer' => config('app.url', 'https://carpia.cl'),
            'X-Title' => 'CarpIA',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'CarpIA/1.0',
        ];
    }

    protected function buildPayload(array $messages, array $options): array
    {
        return [
            'model' => $options['model'] ?? 'meta-llama/llama-3.3-70b-instruct:free',
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
            provider: 'openrouter',
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
            ['id' => 'meta-llama/llama-3.3-70b-instruct:free', 'name' => 'Llama 3.3 70B', 'context_window' => 128000],
            ['id' => 'deepseek/deepseek-r1:free', 'name' => 'DeepSeek R1', 'context_window' => 128000],
            ['id' => 'qwen/qwen-2.5-72b-instruct:free', 'name' => 'Qwen 2.5 72B', 'context_window' => 128000],
            ['id' => 'mistralai/mistral-7b-instruct:free', 'name' => 'Mistral 7B', 'context_window' => 32000],
            ['id' => 'google/gemma-2-9b-it:free', 'name' => 'Gemma 2 9B', 'context_window' => 8192],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.openrouter.api_key'));
    }
}
