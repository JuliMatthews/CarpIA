<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\DTOs\AIResponseDTO;
use Generator;
use Illuminate\Support\Facades\Http;

class CloudflareProvider implements AIProvider
{
    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);
        $model = $options['model'] ?? '@cf/meta/llama-3.1-8b-instruct';

        $response = Http::withToken(config('ai.providers.cloudflare.api_key'))
            ->timeout(30)
            ->post(
                "https://api.cloudflare.com/client/v4/accounts/" . config('ai.providers.cloudflare.account_id') . "/ai/run/{$model}",
                ['messages' => $messages]
            );

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return new AIResponseDTO(
            content: $data['result']['response'] ?? '',
            model: $model,
            provider: 'cloudflare',
            promptTokens: null,
            completionTokens: null,
            totalTokens: null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $model = $options['model'] ?? '@cf/meta/llama-3.1-8b-instruct';

        $response = Http::withToken(config('ai.providers.cloudflare.api_key'))
            ->withOptions(['stream' => true])
            ->timeout(60)
            ->post(
                "https://api.cloudflare.com/client/v4/accounts/" . config('ai.providers.cloudflare.account_id') . "/ai/run/{$model}",
                ['messages' => $messages, 'stream' => true]
            );

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

    public function getName(): string
    {
        return 'Cloudflare';
    }

    public function getSlug(): string
    {
        return 'cloudflare';
    }
}
