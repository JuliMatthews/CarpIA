<?php

namespace App\AI;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use App\Exceptions\AI\AIException;
use App\Exceptions\AI\AIQuotaExceededException;
use App\Exceptions\AI\AIAuthenticationException;
use App\Exceptions\AI\AIRateLimitException;
use App\Exceptions\AI\AITimeoutException;
use App\Exceptions\AI\AIConnectionException;
use App\Exceptions\AI\AIProviderException;
use App\Exceptions\AI\AIInsufficientBalanceException;
use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

abstract class AbstractAIProvider implements AIProvider
{
    protected string $baseUrl;
    protected string $providerSlug;
    protected string $providerName;
    protected int $defaultTimeout = 30;
    protected int $streamTimeout = 60;
    protected int $connectTimeout = 10;
    protected int $retries = 2;

    abstract protected function getApiKey(): ?string;
    abstract protected function buildHeaders(string $apiKey): array;
    abstract protected function buildPayload(array $messages, array $options): array;
    abstract protected function parseResponse(array $data, string $model): AIResponseDTO;
    abstract protected function parseStreamLine(string $line): ?string;

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? $this->getDefaultModel();
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

        $url = $this->buildUrl('chat/completions');
        $headers = $this->buildHeaders($apiKey);
        $payload = $this->buildPayload($messages, $options);

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->defaultTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retries, 1000)
                ->post($url, $payload);

            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $statusCode = $response->status();
            $data = $response->json() ?? [];

            $this->logResponse($url, $model, $statusCode, $elapsed, $data, $options['user_id'] ?? null);

            if ($response->failed()) {
                $this->throwByStatus($statusCode, $data['error']['message'] ?? $data['message'] ?? "HTTP {$statusCode}", $statusCode);
            }

            return $this->parseResponse($data, $model);

        } catch (AIException $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);
            throw $e;

        } catch (\Exception $e) {
            $elapsed = (int) ((microtime(true) - $start) * 1000);
            $this->logError($url, $model, 0, $elapsed, $e, $options['user_id'] ?? null);

            $message = $e->getMessage();

            if (str_contains($message, 'cURL error') || str_contains($message, 'Connection')) {
                throw AIConnectionException::provider($this->providerName, $message, $e);
            }

            throw AIProviderException::provider($this->providerName, $message, $e);
        }
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $model = $options['model'] ?? $this->getDefaultModel();
        $apiKey = $this->getApiKey();

        $this->validateApiKey($apiKey);

        $url = $this->buildUrl('chat/completions');
        $headers = $this->buildHeaders($apiKey);
        $payload = array_merge($this->buildPayload($messages, $options), ['stream' => true]);

        try {
            $response = Http::withHeaders($headers)
                ->withOptions(['stream' => true])
                ->timeout($this->streamTimeout)
                ->connectTimeout($this->connectTimeout)
                ->post($url, $payload);

            if ($response->failed()) {
                $statusCode = $response->status();
                $data = $response->json() ?? [];
                $this->throwByStatus($statusCode, $data['error']['message'] ?? $data['message'] ?? "HTTP {$statusCode}", $statusCode);
            }

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

                $content = $this->parseStreamLine($data);

                if ($content !== null) {
                    yield $content;
                }
            }

        } catch (AIException $e) {
            throw $e;

        } catch (\Exception $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'cURL error') || str_contains($message, 'Connection')) {
                throw AIConnectionException::provider($this->providerName, $message, $e);
            }

            throw AIProviderException::provider($this->providerName, $message, $e);
        }
    }

    protected function validateApiKey(?string $apiKey): void
    {
        if (empty($apiKey)) {
            throw AIAuthenticationException::missing($this->providerName);
        }
    }

    protected function buildUrl(string $endpoint): string
    {
        return "{$this->baseUrl}/{$endpoint}";
    }

    protected function handleError($response, array $data): void
    {
        if (!$response->successful()) {
            $statusCode = $response->status();

            if (isset($data['error'])) {
                $message = $data['error']['message'] ?? $data['error'] ?? 'Error desconocido';
                $code = $data['error']['code'] ?? $statusCode;
            } else {
                $message = $data['message'] ?? $data['error'] ?? "HTTP {$statusCode}";
                $code = $statusCode;
            }

            $this->throwByStatus($statusCode, $message, $code);
        }

        if (isset($data['error'])) {
            $message = $data['error']['message'] ?? 'Error desconocido del proveedor';
            throw AIProviderException::provider($this->providerName, $message);
        }
    }

    protected function throwByStatus(int $statusCode, string $message, $code = null): void
    {
        match (true) {
            $statusCode === 401 => throw AIAuthenticationException::provider($this->providerName, $message),
            $statusCode === 402 => throw AIInsufficientBalanceException::provider($this->providerName, $message),
            $statusCode === 403 => throw AIProviderException::forbidden($this->providerName, $message),
            $statusCode === 429 => throw AIRateLimitException::provider($this->providerName, $message),
            $statusCode >= 500 => throw AIProviderException::serverError($this->providerName, $message),
            default => throw AIProviderException::provider($this->providerName, $message),
        };
    }

    protected function logResponse(string $url, string $model, int $status, int $elapsedMs, array $data, ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();

        Log::info("{$this->providerName} API response", [
            'provider' => $this->providerSlug,
            'model' => $model,
            'url' => $url,
            'status' => $status,
            'elapsed_ms' => $elapsedMs,
            'user_id' => $userId,
            'response_body' => substr(json_encode($data), 0, 500),
        ]);
    }

    protected function logError(string $url, string $model, int $status, int $elapsedMs, Throwable $exception, ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();

        Log::error("{$this->providerName} API error", [
            'provider' => $this->providerSlug,
            'model' => $model,
            'url' => $url,
            'status' => $status,
            'elapsed_ms' => $elapsedMs,
            'user_id' => $userId,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    protected function getDefaultModel(): string
    {
        $models = $this->getAvailableModels();
        return $models[0]['id'] ?? 'unknown';
    }

    public function getName(): string
    {
        return $this->providerName;
    }

    public function getSlug(): string
    {
        return $this->providerSlug;
    }
}
