<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;

class HuggingFaceProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://api-inference.huggingface.co';
    protected string $providerSlug = 'huggingface';
    protected string $providerName = 'HuggingFace';
    protected int $defaultTimeout = 60;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 1;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.huggingface.api_key');
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

    protected function buildUrl(string $endpoint): string
    {
        $model = request()->get('model', 'mistralai/Mistral-7B-Instruct-v0.3');
        return "{$this->baseUrl}/models/{$model}";
    }

    protected function buildPayload(array $messages, array $options): array
    {
        $prompt = $this->formatAsPrompt($messages);

        return [
            'inputs' => $prompt,
            'parameters' => [
                'max_new_tokens' => $options['max_tokens'] ?? 2048,
                'temperature' => $options['temperature'] ?? 0.7,
                'return_full_text' => false,
            ],
        ];
    }

    protected function parseResponse(array $data, string $model): AIResponseDTO
    {
        $content = is_array($data) && isset($data[0]['generated_text'])
            ? $data[0]['generated_text']
            : ($data['generated_text'] ?? '');

        return new AIResponseDTO(
            content: $content,
            model: $model,
            provider: 'huggingface',
            promptTokens: null,
            completionTokens: null,
            totalTokens: null,
            responseTimeMs: null,
        );
    }

    protected function parseStreamLine(string $line): ?string
    {
        $json = json_decode($line, true);

        if (isset($json['token']['text'])) {
            return $json['token']['text'];
        }

        return null;
    }

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? 'mistralai/Mistral-7B-Instruct-v0.3';
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

        $url = "{$this->baseUrl}/models/{$model}";
        $payload = $this->buildPayload($messages, $options);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($this->buildHeaders($apiKey))
                ->timeout($this->defaultTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retries, 1000)
                ->post($url, $payload);

            $data = $response->json();
            $elapsed = (int) ((microtime(true) - $start) * 1000);

            $this->logResponse($url, $model, $response->status(), $elapsed, $data, $options['user_id'] ?? null);

            if (isset($data['error'])) {
                throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, $data['error']);
            }

            $content = is_array($data) && isset($data[0]['generated_text'])
                ? $data[0]['generated_text']
                : ($data['generated_text'] ?? '');

            if (empty($content)) {
                throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, 'Respuesta vacía del servidor');
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

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);
            throw \App\Exceptions\AI\AIConnectionException::provider($this->providerName, $e->getMessage(), $e);

        } catch (\App\Exceptions\AI\AIException $e) {
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
        $model = $options['model'] ?? 'mistralai/Mistral-7B-Instruct-v0.3';
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

        $url = "{$this->baseUrl}/models/{$model}";
        $payload = $this->buildPayload($messages, $options);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($this->buildHeaders($apiKey))
                ->withOptions(['stream' => true])
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

                $data = substr($line, 6);

                $json = json_decode($data, true);

                if (isset($json['token']['text'])) {
                    yield $json['token']['text'];
                }
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw \App\Exceptions\AI\AIConnectionException::provider($this->providerName, $e->getMessage(), $e);

        } catch (\App\Exceptions\AI\AIException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, $e->getMessage(), $e);
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
