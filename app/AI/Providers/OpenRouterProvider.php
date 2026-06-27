<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;

class OpenRouterProvider implements AIProvider
{
    private string $baseUrl = 'https://openrouter.ai/api/v1';

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.providers.openrouter.api_key'),
            'HTTP-Referer' => config('app.url'),
            'X-Title' => 'CarpIA',
        ])
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $options['model'] ?? 'meta-llama/llama-3.3-70b-instruct:free',
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ]);

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'] ?? $options['model'] ?? 'unknown',
            provider: 'openrouter',
            promptTokens: $data['usage']['prompt_tokens'] ?? null,
            completionTokens: $data['usage']['completion_tokens'] ?? null,
            totalTokens: $data['usage']['total_tokens'] ?? null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.providers.openrouter.api_key'),
            'HTTP-Referer' => config('app.url'),
            'X-Title' => 'CarpIA',
        ])
            ->withOptions(['stream' => true])
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $options['model'] ?? 'meta-llama/llama-3.3-70b-instruct:free',
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

    public function getName(): string
    {
        return 'OpenRouter';
    }

    public function getSlug(): string
    {
        return 'openrouter';
    }
}
