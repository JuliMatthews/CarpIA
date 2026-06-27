<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;

class DeepSeekProvider implements AIProvider
{
    private string $baseUrl = 'https://api.deepseek.com/v1';

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);

        $response = Http::withToken(config('ai.providers.deepseek.api_key'))
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $options['model'] ?? 'deepseek-chat',
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ]);

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'] ?? $options['model'] ?? 'unknown',
            provider: 'deepseek',
            promptTokens: $data['usage']['prompt_tokens'] ?? null,
            completionTokens: $data['usage']['completion_tokens'] ?? null,
            totalTokens: $data['usage']['total_tokens'] ?? null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $response = Http::withToken(config('ai.providers.deepseek.api_key'))
            ->withOptions(['stream' => true])
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $options['model'] ?? 'deepseek-chat',
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
                'stream' => true,
            ]);

        $body = $response->body();
        $lines = explode("\n", $body);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || !str_starts_with($line, 'data: ')) {
                continue;
            }

            $data = substr($line, 6);

            if ($data === '[DONE]') {
                break;
            }

            $json = json_decode($data, true);

            if (isset($json['choices'][0]['delta']['content'])) {
                yield $json['choices'][0]['delta']['content'];
            }
        }
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

    public function getName(): string
    {
        return 'DeepSeek';
    }

    public function getSlug(): string
    {
        return 'deepseek';
    }
}
