<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;
use App\Exceptions\AI\AIInsufficientBalanceException;

class DeepSeekProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://api.deepseek.com/v1';
    protected string $providerSlug = 'deepseek';
    protected string $providerName = 'DeepSeek';
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.deepseek.api_key');
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
            'model' => $options['model'] ?? 'deepseek-chat',
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
            provider: 'deepseek',
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

    protected function handleError($response, array $data): void
    {
        if (!$response->successful()) {
            $statusCode = $response->status();

            if (isset($data['error'])) {
                $message = $data['error']['message'] ?? $data['error'] ?? 'Error desconocido';

                if (str_contains(strtolower($message), 'insufficient') || str_contains(strtolower($message), 'balance')) {
                    throw AIInsufficientBalanceException::provider($this->providerName, $message);
                }
            }

            parent::handleError($response, $data);
        }

        parent::handleError($response, $data);
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'deepseek-chat', 'name' => 'DeepSeek V3', 'context_window' => 65536],
            ['id' => 'deepseek-reasoner', 'name' => 'DeepSeek R1', 'context_window' => 65536],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.deepseek.api_key'));
    }
}
