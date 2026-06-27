# AI.md — CarpIA.cl
# Referencia técnica de proveedores de IA

---

## INTERFAZ COMÚN (AIProvider)

Todos los proveedores implementan esta interfaz:

```php
interface AIProvider
{
    public function sendMessage(array $messages, array $options = []): AIResponseDTO;
    public function streamMessage(array $messages, array $options = []): Generator;
    public function getAvailableModels(): array;
    public function isAvailable(): bool;
    public function getName(): string;
    public function getSlug(): string;
}
```

### AIResponseDTO
```php
final class AIResponseDTO
{
    public function __construct(
        public readonly string $content,
        public readonly string $model,
        public readonly string $provider,
        public readonly ?int $promptTokens = null,
        public readonly ?int $completionTokens = null,
        public readonly ?int $totalTokens = null,
        public readonly ?int $responseTimeMs = null,
        public readonly ?float $cost = null,
    ) {}
}
```

---

## FORMATO DE MENSAJES (estándar OpenAI)

Todos los providers compatibles usan este formato:

```php
$messages = [
    ['role' => 'system', 'content' => 'Eres un asistente útil.'],
    ['role' => 'user', 'content' => 'Hola, ¿cómo estás?'],
    ['role' => 'assistant', 'content' => 'Estoy bien, ¿en qué te ayudo?'],
    ['role' => 'user', 'content' => 'Necesito ayuda con Laravel'],
];
```

---

## PROVEEDORES

---

### GROQ
**Documentación:** https://console.groq.com/docs
**Base URL:** `https://api.groq.com/openai/v1`
**Autenticación:** Bearer token
**Formato:** Compatible OpenAI 100%

**Modelos gratuitos:**
| Modelo                     | Contexto  | Velocidad   |
|----------------------------|-----------|-------------|
| `llama-3.3-70b-versatile`  | 128k      | Muy rápida  |
| `llama-3.1-8b-instant`     | 128k      | Ultra rápida|
| `mixtral-8x7b-32768`       | 32k       | Rápida      |
| `gemma2-9b-it`             | 8k        | Rápida      |

**Ejemplo de implementación:**
```php
class GroqProvider implements AIProvider
{
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function sendMessage(array $messages, array $options = []): AIResponseDTO
    {
        $start = microtime(true);

        $response = Http::withToken(config('ai.groq.api_key'))
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'       => $options['model'] ?? 'llama-3.3-70b-versatile',
                'messages'    => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens'  => $options['max_tokens'] ?? 2048,
            ]);

        $data = $response->json();
        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return new AIResponseDTO(
            content: $data['choices'][0]['message']['content'],
            model: $data['model'],
            provider: 'groq',
            promptTokens: $data['usage']['prompt_tokens'] ?? null,
            completionTokens: $data['usage']['completion_tokens'] ?? null,
            totalTokens: $data['usage']['total_tokens'] ?? null,
            responseTimeMs: $elapsed,
        );
    }

    public function streamMessage(array $messages, array $options = []): Generator
    {
        $response = Http::withToken(config('ai.groq.api_key'))
            ->withOptions(['stream' => true])
            ->post("{$this->baseUrl}/chat/completions", [
                'model'    => $options['model'] ?? 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'stream'   => true,
            ]);

        foreach (explode("\n", $response->body()) as $line) {
            if (str_starts_with($line, 'data: ') && $line !== 'data: [DONE]') {
                $data = json_decode(substr($line, 6), true);
                if (isset($data['choices'][0]['delta']['content'])) {
                    yield $data['choices'][0]['delta']['content'];
                }
            }
        }
    }
}
```

---

### GEMINI
**Documentación:** https://ai.google.dev/docs
**Base URL:** `https://generativelanguage.googleapis.com/v1beta`
**Autenticación:** API key como query param `?key=`
**Formato:** Propio (diferente a OpenAI)

**Modelos gratuitos:**
| Modelo                    | Contexto  | Notas              |
|---------------------------|-----------|--------------------|
| `gemini-2.0-flash`        | 1M tokens | Recomendado        |
| `gemini-1.5-flash`        | 1M tokens | Estable            |
| `gemini-1.5-flash-8b`     | 1M tokens | Ultra rápido       |

**Formato de request:**
```php
// Conversión de formato OpenAI → Gemini
$contents = collect($messages)
    ->filter(fn($m) => $m['role'] !== 'system')
    ->map(fn($m) => [
        'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
        'parts' => [['text' => $m['content']]],
    ])->values()->all();

$systemInstruction = collect($messages)
    ->firstWhere('role', 'system');

$payload = [
    'contents' => $contents,
];

if ($systemInstruction) {
    $payload['system_instruction'] = [
        'parts' => [['text' => $systemInstruction['content']]]
    ];
}

$response = Http::post(
    "{$this->baseUrl}/models/{$model}:generateContent?key=" . config('ai.gemini.api_key'),
    $payload
);
```

---

### OPENROUTER
**Documentación:** https://openrouter.ai/docs
**Base URL:** `https://openrouter.ai/api/v1`
**Autenticación:** Bearer token
**Formato:** Compatible OpenAI 100%

**Headers adicionales requeridos:**
```php
Http::withHeaders([
    'Authorization'       => 'Bearer ' . config('ai.openrouter.api_key'),
    'HTTP-Referer'        => config('app.url'),
    'X-Title'             => 'CarpIA',
]);
```

**Modelos gratuitos (selección):**
| Modelo                              | Provider   |
|-------------------------------------|------------|
| `meta-llama/llama-3.3-70b-instruct:free` | Meta  |
| `deepseek/deepseek-r1:free`         | DeepSeek   |
| `qwen/qwen-2.5-72b-instruct:free`   | Qwen       |
| `mistralai/mistral-7b-instruct:free`| Mistral    |
| `google/gemma-2-9b-it:free`         | Google     |

> Los modelos con `:free` son gratuitos en OpenRouter.

---

### CLOUDFLARE AI
**Documentación:** https://developers.cloudflare.com/workers-ai
**Base URL:** `https://api.cloudflare.com/client/v4/accounts/{account_id}/ai/run`
**Autenticación:** Bearer token (API key de Cloudflare)
**Formato:** Propio

**Ejemplo:**
```php
$response = Http::withToken(config('ai.cloudflare.api_key'))
    ->post(
        "https://api.cloudflare.com/client/v4/accounts/" . config('ai.cloudflare.account_id') . "/ai/run/@cf/meta/llama-3.1-8b-instruct",
        ['messages' => $messages]
    );

$content = $response->json('result.response');
```

**Modelos disponibles:**
- `@cf/meta/llama-3.1-8b-instruct`
- `@cf/mistral/mistral-7b-instruct-v0.1`
- `@cf/microsoft/phi-2`
- `@cf/google/gemma-7b-it`

---

### HUGGINGFACE
**Documentación:** https://huggingface.co/docs/api-inference
**Base URL:** `https://api-inference.huggingface.co/models`
**Autenticación:** Bearer token
**Formato:** Propio (varía por modelo)

```php
$response = Http::withToken(config('ai.huggingface.api_key'))
    ->post(
        "https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.3",
        ['inputs' => $formattedPrompt, 'parameters' => ['max_new_tokens' => 512]]
    );
```

---

### MISTRAL
**Documentación:** https://docs.mistral.ai
**Base URL:** `https://api.mistral.ai/v1`
**Autenticación:** Bearer token
**Formato:** Compatible OpenAI

**Modelos gratuitos:**
- `mistral-small-latest` (con free tier)
- `open-mistral-7b`
- `open-mixtral-8x7b`

---

### DEEPSEEK
**Documentación:** https://platform.deepseek.com/docs
**Base URL:** `https://api.deepseek.com/v1`
**Autenticación:** Bearer token
**Formato:** Compatible OpenAI

**Modelos:**
- `deepseek-chat` (DeepSeek V3)
- `deepseek-reasoner` (R1)

---

### OLLAMA (LOCAL)
**Documentación:** https://github.com/ollama/ollama/blob/main/docs/api.md
**Base URL:** `http://localhost:11434/api`
**Autenticación:** Sin key
**Formato:** Propio (pero también soporta `/v1/chat/completions` compatible con OpenAI)

```php
// Endpoint compatible con OpenAI (recomendado)
$response = Http::post('http://localhost:11434/v1/chat/completions', [
    'model'    => 'llama3.2',
    'messages' => $messages,
]);

// O endpoint nativo
$response = Http::post('http://localhost:11434/api/chat', [
    'model'    => 'llama3.2',
    'messages' => $messages,
    'stream'   => false,
]);
```

**Verificar disponibilidad:**
```php
public function isAvailable(): bool
{
    try {
        $response = Http::timeout(2)->get('http://localhost:11434/api/tags');
        return $response->successful();
    } catch (\Exception) {
        return false;
    }
}
```

---

## CONFIG/AI.PHP

```php
return [
    'default' => env('AI_DEFAULT_PROVIDER', 'groq'),

    'providers' => [
        'groq' => [
            'api_key'  => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
        ],
        'gemini' => [
            'api_key'  => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ],
        'openrouter' => [
            'api_key'  => env('OPENROUTER_API_KEY'),
            'base_url' => 'https://openrouter.ai/api/v1',
        ],
        'cloudflare' => [
            'api_key'    => env('CLOUDFLARE_API_KEY'),
            'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        ],
        'huggingface' => [
            'api_key'  => env('HUGGINGFACE_API_KEY'),
            'base_url' => 'https://api-inference.huggingface.co',
        ],
        'mistral' => [
            'api_key'  => env('MISTRAL_API_KEY'),
            'base_url' => 'https://api.mistral.ai/v1',
        ],
        'deepseek' => [
            'api_key'  => env('DEEPSEEK_API_KEY'),
            'base_url' => 'https://api.deepseek.com/v1',
        ],
        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
        ],
    ],
];
```

---

## AIMANAGER

```php
class AIManager
{
    private array $providers = [];

    public function __construct(
        private GroqProvider $groq,
        private GeminiProvider $gemini,
        private OpenRouterProvider $openRouter,
        // ...
    ) {
        $this->providers = [
            'groq'        => $groq,
            'gemini'      => $gemini,
            'openrouter'  => $openRouter,
        ];
    }

    public function provider(string $slug): AIProvider
    {
        if (!isset($this->providers[$slug])) {
            throw new \InvalidArgumentException("Proveedor '{$slug}' no encontrado.");
        }

        return $this->providers[$slug];
    }

    public function forModel(AiModel $model): AIProvider
    {
        return $this->provider($model->provider->slug);
    }
}
```

---

## RATE LIMITS (GRATUITOS)

| Proveedor  | RPM   | RPD    | TPM       |
|------------|-------|--------|-----------|
| Groq       | 30    | 14,400 | 6,000     |
| Gemini 2.0 | 15    | 1,500  | 1,000,000 |
| OpenRouter | Varía | Varía  | Varía     |
| Mistral    | 1     | -      | 500,000   |
| DeepSeek   | Varía | Varía  | Varía     |
| Ollama     | ∞     | ∞      | ∞ (local) |

> RPM = requests/minute, RPD = requests/day, TPM = tokens/minute
