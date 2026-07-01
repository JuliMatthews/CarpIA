<?php

namespace App\AI\Providers;

use App\AI\AbstractAIProvider;
use App\DTOs\AIResponseDTO;
use App\Exceptions\AI\AIAuthenticationException;

class CloudflareProvider extends AbstractAIProvider
{
    protected string $baseUrl = 'https://api.cloudflare.com/client/v4/accounts';
    protected string $providerSlug = 'cloudflare';
    protected string $providerName = 'Cloudflare';
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    protected function getApiKey(): ?string
    {
        return config('ai.providers.cloudflare.api_key');
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
        $accountId = config('ai.providers.cloudflare.account_id');
        $model = request()->get('model', '@cf/meta/llama-3.1-8b-instruct');
        return "{$this->baseUrl}/{$accountId}/ai/run/{$model}";
    }

    protected function buildPayload(array $messages, array $options): array
    {
        return ['messages' => $messages];
    }

    protected function parseResponse(array $data, string $model): AIResponseDTO
    {
        return new AIResponseDTO(
            content: $data['result']['response'],
            model: $model,
            provider: 'cloudflare',
            promptTokens: null,
            completionTokens: null,
            totalTokens: null,
            responseTimeMs: null,
        );
    }

    protected function parseStreamLine(string $line): ?string
    {
        $json = json_decode($line, true);

        if (isset($json['response'])) {
            return $json['response'];
        }

        return null;
    }

    protected function validateApiKey(?string $apiKey): void
    {
        $accountId = config('ai.providers.cloudflare.account_id');

        if (empty($apiKey) || empty($accountId)) {
            throw AIAuthenticationException::missing($this->providerName);
        }
    }

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? '@cf/meta/llama-3.1-8b-instruct';
        $apiKey = $this->getApiKey();
        $accountId = config('ai.providers.cloudflare.account_id');

        $this->validateApiKey($apiKey);

        $url = "{$this->baseUrl}/{$accountId}/ai/run/{$model}";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($this->buildHeaders($apiKey))
                ->timeout($this->defaultTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retries, 1000)
                ->post($url, ['messages' => $messages]);

            $data = $response->json();
            $elapsed = (int) ((microtime(true) - $start) * 1000);

            $this->logResponse($url, $model, $response->status(), $elapsed, $data, $options['user_id'] ?? null);

            if (isset($data['errors']) && !empty($data['errors'])) {
                $message = $data['errors'][0]['message'] ?? 'Error desconocido de Cloudflare';
                throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, $message);
            }

            if (!isset($data['result']['response'])) {
                throw \App\Exceptions\AI\AIProviderException::provider($this->providerName, 'Respuesta inválida del servidor');
            }

            return new AIResponseDTO(
                content: $data['result']['response'],
                model: $model,
                provider: 'cloudflare',
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
        $model = $options['model'] ?? '@cf/meta/llama-3.1-8b-instruct';
        $apiKey = $this->getApiKey();
        $accountId = config('ai.providers.cloudflare.account_id');

        $this->validateApiKey($apiKey);

        $url = "{$this->baseUrl}/{$accountId}/ai/run/{$model}";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($this->buildHeaders($apiKey))
                ->withOptions(['stream' => true])
                ->timeout($this->streamTimeout)
                ->connectTimeout($this->connectTimeout)
                ->post($url, ['messages' => $messages, 'stream' => true]);

            $body = $response->body();
            $lines = explode("\n", $body);

            foreach ($lines as $line) {
                $line = trim($line);

                if (empty($line) || !str_starts_with($line, 'data: ')) {
                    continue;
                }

                $data = substr($line, 6);

                $json = json_decode($data, true);

                if (isset($json['response'])) {
                    yield $json['response'];
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
            ['id' => '@cf/meta/llama-3.1-8b-instruct', 'name' => 'Llama 3.1 8B', 'context_window' => 8192],
            ['id' => '@cf/mistral/mistral-7b-instruct-v0.1', 'name' => 'Mistral 7B', 'context_window' => 8192],
            ['id' => '@cf/microsoft/phi-2', 'name' => 'Phi 2', 'context_window' => 2048],
            ['id' => '@cf/google/gemma-7b-it', 'name' => 'Gemma 7B', 'context_window' => 8192],
        ];
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.cloudflare.api_key'))
            && !empty(config('ai.providers.cloudflare.account_id'));
    }
}
