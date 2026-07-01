<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;
use App\Exceptions\AI\AIException;

class GeminiProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected string $providerSlug = 'gemini';
    protected string $providerName = 'Gemini';
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.gemini.api_key');
    }

    protected function buildHeaders(string $apiKey): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'CarpIA/1.0',
        ];
    }

    protected function buildUrl(string $endpoint): string
    {
        $model = request()->get('model', 'gemini-2.0-flash');
        $apiKey = $this->getApiKey();
        return "{$this->baseUrl}/models/{$model}:generateContent?key={$apiKey}";
    }

    protected function buildPayload(array $messages, array $options): array
    {
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

        return $payload;
    }

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? 'gemini-2.0-flash';
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

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

        $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = \Illuminate\Support\Facades\Http::timeout($this->defaultTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retries, 1000)
                ->post($url, $payload);

            $data = $response->json();
            $elapsed = (int) ((microtime(true) - $start) * 1000);

            $this->logResponse($url, $model, $response->status(), $elapsed, $data, $options['user_id'] ?? null);

            $this->handleGeminiError($response, $data, $model);

            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($content)) {
                $finishReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
                throw AIException::provider($this->providerName, "Respuesta vacía. finishReason: {$finishReason}");
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

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);
            throw \App\Exceptions\AI\AIConnectionException::provider($this->providerName, $e->getMessage(), $e);

        } catch (AIException $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);
            throw $e;

        } catch (\Throwable $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);
            throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, $e->getMessage(), $e);
        }
    }

    public function streamMessage(array $messages, array $options = []): \Generator
    {
        $model = $options['model'] ?? 'gemini-2.0-flash';
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

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

        $url = "{$this->baseUrl}/models/{$model}:streamGenerateContent?key={$apiKey}&alt=sse";

        try {
            $response = \Illuminate\Support\Facades\Http::withOptions(['stream' => true])
                ->timeout($this->streamTimeout)
                ->connectTimeout($this->connectTimeout)
                ->post($url, $payload);

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

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw \App\Exceptions\AI\AIConnectionException::provider($this->providerName, $e->getMessage(), $e);

        } catch (AIException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, $e->getMessage(), $e);
        }
    }

    protected function handleGeminiError($response, array $data, string $model): void
    {
        if (!$response->successful()) {
            $statusCode = $response->status();

            if (isset($data['error'])) {
                $message = $data['error']['message'] ?? 'Error desconocido de Gemini';
                $code = $data['error']['code'] ?? $statusCode;

                if (str_contains(strtolower($message), 'quota') || str_contains(strtolower($message), 'limit')) {
                    throw \App\Exceptions\AI\AIQuotaExceededException::provider($this->providerName, $message);
                }

                if ($statusCode === 429) {
                    throw \App\Exceptions\AI\AIRateLimitException::provider($this->providerName, $message);
                }

                if ($statusCode === 401) {
                    throw \App\Exceptions\AI\AIAuthenticationException::provider($this->providerName, $message);
                }
            }

            $this->throwByStatus($statusCode, $data['error']['message'] ?? "HTTP {$statusCode}", $statusCode);
        }

        if (isset($data['error'])) {
            throw AIException::provider($this->providerName, $data['error']['message'] ?? 'Error desconocido');
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

    protected function parseResponse(array $data, string $model): AIResponseDTO
    {
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return new AIResponseDTO(
            content: $content,
            model: $model,
            provider: 'gemini',
            promptTokens: $data['usageMetadata']['promptTokenCount'] ?? null,
            completionTokens: $data['usageMetadata']['candidatesTokenCount'] ?? null,
            totalTokens: $data['usageMetadata']['totalTokenCount'] ?? null,
            responseTimeMs: null,
        );
    }

    protected function parseStreamLine(string $line): ?string
    {
        $json = json_decode($line, true);

        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            return $json['candidates'][0]['content']['parts'][0]['text'];
        }

        return null;
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
