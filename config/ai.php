<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proveedor por defecto
    |--------------------------------------------------------------------------
    */

    'default' => env('AI_DEFAULT_PROVIDER', 'mistral'),

    /*
    |--------------------------------------------------------------------------
    | Configuración de proveedores
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
            'models' => [
                'llama-3.3-70b-versatile',
                'llama-3.1-8b-instant',
                'mixtral-8x7b-32768',
                'gemma2-9b-it',
            ],
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'models' => [
                'gemini-2.0-flash',
                'gemini-1.5-flash',
                'gemini-1.5-flash-8b',
            ],
        ],

        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => 'https://openrouter.ai/api/v1',
            'models' => [
                'meta-llama/llama-3.3-70b-instruct:free',
                'deepseek/deepseek-r1:free',
                'qwen/qwen-2.5-72b-instruct:free',
                'mistralai/mistral-7b-instruct:free',
                'google/gemma-2-9b-it:free',
            ],
        ],

        'cloudflare' => [
            'api_key' => env('CLOUDFLARE_API_KEY'),
            'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
            'models' => [
                '@cf/meta/llama-3.1-8b-instruct',
                '@cf/mistral/mistral-7b-instruct-v0.1',
                '@cf/microsoft/phi-2',
                '@cf/google/gemma-7b-it',
            ],
        ],

        'huggingface' => [
            'api_key' => env('HUGGINGFACE_API_KEY'),
            'base_url' => 'https://api-inference.huggingface.co',
            'models' => [
                'mistralai/Mistral-7B-Instruct-v0.3',
            ],
        ],

        'mistral' => [
            'api_key' => env('MISTRAL_API_KEY'),
            'base_url' => 'https://api.mistral.ai/v1',
            'models' => [
                'mistral-small-latest',
                'open-mistral-7b',
                'open-mixtral-8x7b',
            ],
        ],

        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => 'https://api.deepseek.com/v1',
            'models' => [
                'deepseek-chat',
                'deepseek-reasoner',
            ],
        ],

        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'models' => [
                'llama3.2',
            ],
        ],

    ],

];
