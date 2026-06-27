<?php

namespace App\Providers;

use App\AI\AIManager;
use App\AI\Contracts\AIProvider;
use App\AI\Providers\CloudflareProvider;
use App\AI\Providers\DeepSeekProvider;
use App\AI\Providers\GeminiProvider;
use App\AI\Providers\GroqProvider;
use App\AI\Providers\HuggingFaceProvider;
use App\AI\Providers\MistralProvider;
use App\AI\Providers\OllamaProvider;
use App\AI\Providers\OpenRouterProvider;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GroqProvider::class, fn() => new GroqProvider());
        $this->app->singleton(GeminiProvider::class, fn() => new GeminiProvider());
        $this->app->singleton(OpenRouterProvider::class, fn() => new OpenRouterProvider());
        $this->app->singleton(CloudflareProvider::class, fn() => new CloudflareProvider());
        $this->app->singleton(HuggingFaceProvider::class, fn() => new HuggingFaceProvider());
        $this->app->singleton(MistralProvider::class, fn() => new MistralProvider());
        $this->app->singleton(DeepSeekProvider::class, fn() => new DeepSeekProvider());
        $this->app->singleton(OllamaProvider::class, fn() => new OllamaProvider());

        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager(
                $app->make(GroqProvider::class),
                $app->make(GeminiProvider::class),
                $app->make(OpenRouterProvider::class),
                $app->make(CloudflareProvider::class),
                $app->make(HuggingFaceProvider::class),
                $app->make(MistralProvider::class),
                $app->make(DeepSeekProvider::class),
                $app->make(OllamaProvider::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
