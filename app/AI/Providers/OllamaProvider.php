<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;

class OllamaProvider implements AIProvider
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('ai.providers.ollama.base_url', 'http://localhost:11434');
    }

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? 'llama3.2';

        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/v1/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ]);

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'] ?? $model,
            provider: 'ollama',
            promptTokens: $data['usage']['prompt_tokens'] ?? null,
            completionTokens: $data['usage']['completion_tokens'] ?? null,
            totalTokens: $data['usage']['total_tokens'] ?? null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $model = $options['model'] ?? 'llama3.2';

        $response = Http::withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->baseUrl}/v1/chat/completions", [
                'model' => $model,
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

    public function getName(): string
    {
        return 'Ollama';
    }

    public function getSlug(): string
    {
        return 'ollama';
    }
}
