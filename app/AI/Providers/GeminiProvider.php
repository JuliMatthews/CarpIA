<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AIProvider
{
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? 'gemini-2.0-flash';

        [$contents, $systemInstruction] = $this->formatMessages($messages);

        $payload = ['contents' => $contents];

        if ($systemInstruction) {
            $payload['system_instruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        $payload['generationConfig'] = [
            'maxOutputTokens' => $options['max_tokens'] ?? 2048,
        ];

        if (isset($options['temperature'])) {
            $payload['generationConfig']['temperature'] = $options['temperature'];
        }

        $apiKey = config('ai.providers.gemini.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Gemini API key no configurada. Agrega GEMINI_API_KEY en tu .env');
        }

        $response = Http::timeout(30)
            ->post(
                "{$this->baseUrl}/models/{$model}:generateContent?key={$apiKey}",
                $payload
            );

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        if (isset($data['error'])) {
            $code = $data['error']['code'] ?? 500;
            $message = $data['error']['message'] ?? 'Error desconocido de Gemini';
            throw new RuntimeException("Gemini API error ({$code}): {$message}");
        }

        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($content)) {
            $finishReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            throw new RuntimeException("Gemini devolvió respuesta vacía. finishReason: {$finishReason}");
        }

        return new AIResponseDTO(
            content: $content,
            model: $model,
            provider: 'gemini',
            promptTokens: $data['usageMetadata']['promptTokenCount'] ?? null,
            completionTokens: $data['usageMetadata']['candidatesTokenCount'] ?? null,
            totalTokens: $data['usageMetadata']['totalTokenCount'] ?? null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $model = $options['model'] ?? 'gemini-2.0-flash';

        [$contents, $systemInstruction] = $this->formatMessages($messages);

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => $options['max_tokens'] ?? 2048,
            ],
        ];

        if (isset($options['temperature'])) {
            $payload['generationConfig']['temperature'] = $options['temperature'];
        }

        if ($systemInstruction) {
            $payload['system_instruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        $apiKey = config('ai.providers.gemini.api_key');

        $response = Http::withOptions(['stream' => true])
            ->timeout(60)
            ->post(
                "{$this->baseUrl}/models/{$model}:streamGenerateContent?key={$apiKey}&alt=sse",
                $payload
            );

        $body = $response->body();
        $lines = explode("\n", $body);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || !str_starts_with($line, 'data: ')) {
                continue;
            }

            $data = json_decode(substr($line, 6), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                yield $data['candidates'][0]['content']['parts'][0]['text'];
            }
        }
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'gemini-2.0-flash', 'name' => 'Gemini 2.0 Flash', 'context_window' => 1000000],
            ['id' => 'gemini-1.5-flash', 'name' => 'Gemini 1.5 Flash', 'context_window' => 1000000],
            ['id' => 'gemini-1.5-flash-8b', 'name' => 'Gemini 1.5 Flash 8B', 'context_window' => 1000000],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.gemini.api_key'));
    }

    public function getName(): string
    {
        return 'Gemini';
    }

    public function getSlug(): string
    {
        return 'gemini';
    }

    private function formatMessages(array $messages): array
    {
        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemInstruction = $message['content'];
                continue;
            }

            $role = $message['role'] === 'assistant' ? 'model' : 'user';

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $message['content']]],
            ];
        }

        return [$contents, $systemInstruction];
    }
}
