<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HuggingFaceProvider implements AIProvider
{
    private string $baseUrl = 'https://api-inference.huggingface.co';

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? 'mistralai/Mistral-7B-Instruct-v0.3';

        $apiKey = config('ai.providers.huggingface.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('HuggingFace API key no configurada. Agrega HUGGINGFACE_API_KEY en tu .env');
        }

        $prompt = $this->formatAsPrompt($messages);

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post("{$this->baseUrl}/models/{$model}", [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => $options['max_tokens'] ?? 2048,
                    'temperature' => $options['temperature'] ?? 0.7,
                    'return_full_text' => false,
                ],
            ]);

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        if (isset($data['error'])) {
            throw new RuntimeException("HuggingFace API error: {$data['error']}");
        }

        $content = is_array($data) && isset($data[0]['generated_text'])
            ? $data[0]['generated_text']
            : ($data['generated_text'] ?? '');

        if (empty($content)) {
            throw new RuntimeException("HuggingFace devolvió respuesta vacía: " . substr(json_encode($data), 0, 200));
        }

        return new AIResponseDTO(
            content: $content,
            model: $model,
            provider: 'huggingface',
            promptTokens: null,
            completionTokens: null,
            totalTokens: null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $model = $options['model'] ?? 'mistralai/Mistral-7B-Instruct-v0.3';
        $prompt = $this->formatAsPrompt($messages);

        $response = Http::withToken(config('ai.providers.huggingface.api_key'))
            ->withOptions(['stream' => true])
            ->timeout(60)
            ->post("{$this->baseUrl}/models/{$model}", [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => $options['max_tokens'] ?? 2048,
                    'temperature' => $options['temperature'] ?? 0.7,
                    'return_full_text' => false,
                ],
            ]);

        $body = $response->body();
        $lines = explode("\n", $body);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || !str_starts_with($line, 'data: ')) {
                continue;
            }

            $data = substr($line, 6);

            $json = json_decode($data, true);

            if (isset($json['token']['text'])) {
                yield $json['token']['text'];
            }
        }
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'mistralai/Mistral-7B-Instruct-v0.3', 'name' => 'Mistral 7B Instruct', 'context_window' => 32768],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.huggingface.api_key'));
    }

    public function getName(): string
    {
        return 'HuggingFace';
    }

    public function getSlug(): string
    {
        return 'huggingface';
    }

    private function formatAsPrompt(array $messages): string
    {
        $prompt = '';

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $prompt .= "[INST] {$message['content']} [/INST]\n";
            } elseif ($message['role'] === 'user') {
                $prompt .= "[INST] {$message['content']} [/INST]\n";
            } elseif ($message['role'] === 'assistant') {
                $prompt .= "{$message['content']}\n";
            }
        }

        return $prompt;
    }
}
