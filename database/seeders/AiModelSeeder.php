<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        $groq = AiProvider::where('slug', 'groq')->first();
        $gemini = AiProvider::where('slug', 'gemini')->first();
        $openrouter = AiProvider::where('slug', 'openrouter')->first();
        $cloudflare = AiProvider::where('slug', 'cloudflare')->first();
        $huggingface = AiProvider::where('slug', 'huggingface')->first();
        $mistral = AiProvider::where('slug', 'mistral')->first();
        $deepseek = AiProvider::where('slug', 'deepseek')->first();
        $ollama = AiProvider::where('slug', 'ollama')->first();

        $models = [
            // Groq
            ['provider_id' => $groq->id, 'name' => 'Llama 3.3 70B', 'slug' => 'llama-3.3-70b-versatile', 'context_window' => 128000, 'is_free' => true],
            ['provider_id' => $groq->id, 'name' => 'Llama 3.1 8B Instant', 'slug' => 'llama-3.1-8b-instant', 'context_window' => 128000, 'is_free' => true],
            ['provider_id' => $groq->id, 'name' => 'Mixtral 8x7B', 'slug' => 'mixtral-8x7b-32768', 'context_window' => 32768, 'is_free' => true],
            ['provider_id' => $groq->id, 'name' => 'Gemma 2 9B', 'slug' => 'gemma2-9b-it', 'context_window' => 8192, 'is_free' => true],

            // Gemini
            ['provider_id' => $gemini->id, 'name' => 'Gemini 2.0 Flash', 'slug' => 'gemini-2.0-flash', 'context_window' => 1000000, 'is_free' => true],
            ['provider_id' => $gemini->id, 'name' => 'Gemini 1.5 Flash', 'slug' => 'gemini-1.5-flash', 'context_window' => 1000000, 'is_free' => true],
            ['provider_id' => $gemini->id, 'name' => 'Gemini 1.5 Flash 8B', 'slug' => 'gemini-1.5-flash-8b', 'context_window' => 1000000, 'is_free' => true],

            // OpenRouter
            ['provider_id' => $openrouter->id, 'name' => 'Llama 3.3 70B (Free)', 'slug' => 'meta-llama/llama-3.3-70b-instruct:free', 'context_window' => 128000, 'is_free' => true],
            ['provider_id' => $openrouter->id, 'name' => 'DeepSeek R1 (Free)', 'slug' => 'deepseek/deepseek-r1:free', 'context_window' => 128000, 'is_free' => true],
            ['provider_id' => $openrouter->id, 'name' => 'Qwen 2.5 72B (Free)', 'slug' => 'qwen/qwen-2.5-72b-instruct:free', 'context_window' => 128000, 'is_free' => true],
            ['provider_id' => $openrouter->id, 'name' => 'Mistral 7B (Free)', 'slug' => 'mistralai/mistral-7b-instruct:free', 'context_window' => 32768, 'is_free' => true],
            ['provider_id' => $openrouter->id, 'name' => 'Gemma 2 9B (Free)', 'slug' => 'google/gemma-2-9b-it:free', 'context_window' => 8192, 'is_free' => true],

            // Cloudflare
            ['provider_id' => $cloudflare->id, 'name' => 'Llama 3.1 8B', 'slug' => '@cf/meta/llama-3.1-8b-instruct', 'context_window' => 8192, 'is_free' => true],
            ['provider_id' => $cloudflare->id, 'name' => 'Mistral 7B', 'slug' => '@cf/mistral/mistral-7b-instruct-v0.1', 'context_window' => 8192, 'is_free' => true],
            ['provider_id' => $cloudflare->id, 'name' => 'Phi-2', 'slug' => '@cf/microsoft/phi-2', 'context_window' => 2048, 'is_free' => true],
            ['provider_id' => $cloudflare->id, 'name' => 'Gemma 7B', 'slug' => '@cf/google/gemma-7b-it', 'context_window' => 8192, 'is_free' => true],

            // HuggingFace
            ['provider_id' => $huggingface->id, 'name' => 'Mistral 7B Instruct v0.3', 'slug' => 'mistralai/Mistral-7B-Instruct-v0.3', 'context_window' => 32768, 'is_free' => true],

            // Mistral
            ['provider_id' => $mistral->id, 'name' => 'Mistral Small', 'slug' => 'mistral-small-latest', 'context_window' => 32768, 'is_free' => true],
            ['provider_id' => $mistral->id, 'name' => 'Open Mistral 7B', 'slug' => 'open-mistral-7b', 'context_window' => 32768, 'is_free' => true],
            ['provider_id' => $mistral->id, 'name' => 'Open Mixtral 8x7B', 'slug' => 'open-mixtral-8x7b', 'context_window' => 32768, 'is_free' => true],

            // DeepSeek
            ['provider_id' => $deepseek->id, 'name' => 'DeepSeek Chat', 'slug' => 'deepseek-chat', 'context_window' => 64000, 'is_free' => true],
            ['provider_id' => $deepseek->id, 'name' => 'DeepSeek Reasoner', 'slug' => 'deepseek-reasoner', 'context_window' => 64000, 'is_free' => true],

            // Ollama
            ['provider_id' => $ollama->id, 'name' => 'Llama 3.2', 'slug' => 'llama3.2', 'context_window' => 128000, 'is_free' => true],
        ];

        foreach ($models as $model) {
            AiModel::updateOrCreate(
                ['provider_id' => $model['provider_id'], 'slug' => $model['slug']],
                $model
            );
        }
    }
}
