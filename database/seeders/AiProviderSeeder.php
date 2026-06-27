<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Groq',
                'slug' => 'groq',
                'base_url' => 'https://api.groq.com/openai/v1',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'Gemini',
                'slug' => 'gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'OpenRouter',
                'slug' => 'openrouter',
                'base_url' => 'https://openrouter.ai/api/v1',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'Cloudflare AI',
                'slug' => 'cloudflare',
                'base_url' => 'https://api.cloudflare.com/client/v4/accounts',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'HuggingFace',
                'slug' => 'huggingface',
                'base_url' => 'https://api-inference.huggingface.co',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'Mistral',
                'slug' => 'mistral',
                'base_url' => 'https://api.mistral.ai/v1',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'DeepSeek',
                'slug' => 'deepseek',
                'base_url' => 'https://api.deepseek.com/v1',
                'is_active' => true,
                'requires_key' => true,
            ],
            [
                'name' => 'Ollama',
                'slug' => 'ollama',
                'base_url' => 'http://localhost:11434',
                'is_active' => true,
                'requires_key' => false,
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::updateOrCreate(
                ['slug' => $provider['slug']],
                $provider
            );
        }
    }
}
