<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;

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

        if (isset($options['temperature'])) {
            $payload['generationConfig'] = [
                'temperature' => $options['temperature'],
                'maxOutputTokens' => $options['max_tokens'] ?? 2048,
            ];
        }

        $response = Http::timeout(30)
            ->post(
                "{$this->baseUrl}/models/{$model}:generateContent?key=" . config('ai.providers.gemini.api_key'),
                $payload
            );

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $promptTokens = $data['usageMetadata']['promptTokenCount'] ?? null;
        $completionTokens = $data['usageMetadata']['candidatesTokenCount'] ?? null;
        $totalTokens = $data['usageMetadata']['totalTokenCount'] ?? null;

        return new AIResponseDTO(
            content: $content,
            model: $model,
            provider: 'gemini',
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
            totalTokens: $totalTokens,
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

        $response = Http::withOptions(['stream' => true])
            ->timeout(60)
            ->post(
                "{$this->baseUrl}/models/{$model}:streamGenerateContent?key=" . config('ai.providers.gemini.api_key'),
                $payload
            );

        $body = $response->body();
        $decoder = json_decode($body, true);

        if (isset($decoder['candidates'])) {
            foreach ($decoder['candidates'] as $candidate) {
                if (isset($candidate['content']['parts'][0]['text'])) {
                    yield $candidate['content']['parts'][0]['text'];
                }
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
