# SKILL.md — CarpIA.cl
# Instrucciones para el agente de IA (OpenCode / Claude)

---

## ROL

Eres un desarrollador Full Stack Senior especializado en Laravel 12, PHP 8.4, Livewire 3, AlpineJS y TailwindCSS. Estás construyendo **CarpIA.cl**, una plataforma SaaS chilena que unifica múltiples modelos de IA en una sola interfaz.

Tu trabajo es escribir código limpio, profesional, escalable y completamente funcional. No generes código de ejemplo ni stubs vacíos: genera implementaciones reales y completas.

---

## STACK OFICIAL

| Capa       | Tecnología                          |
|------------|-------------------------------------|
| Backend    | Laravel 12, PHP 8.4                 |
| Base datos | MySQL 8                             |
| Auth       | Laravel Sanctum                     |
| Cache/Queue| Redis (preparado desde el inicio)   |
| Frontend   | Blade + Livewire 3 + AlpineJS       |
| Estilos    | TailwindCSS 3 (modo oscuro por defecto) |
| Testing    | Pest PHP                            |

**NO usar React, Vue, Inertia ni ningún framework JS externo.**

---

## ARQUITECTURA

```
app/
├── AI/
│   ├── Contracts/
│   │   └── AIProvider.php           ← Interfaz común para todos los proveedores
│   ├── Providers/
│   │   ├── OpenRouterProvider.php
│   │   ├── GroqProvider.php
│   │   ├── GeminiProvider.php
│   │   ├── CloudflareProvider.php
│   │   ├── HuggingFaceProvider.php
│   │   ├── MistralProvider.php
│   │   ├── DeepSeekProvider.php
│   │   └── OllamaProvider.php
│   └── AIManager.php                ← Resuelve qué proveedor usar
│
├── DTOs/
│   ├── MessageDTO.php
│   ├── ConversationDTO.php
│   └── AIResponseDTO.php
│
├── Services/
│   ├── ConversationService.php
│   ├── MessageService.php
│   ├── PromptLibraryService.php
│   └── UserSettingsService.php
│
├── Http/
│   ├── Controllers/                 ← Solo reciben request y delegan al Service
│   └── Livewire/
│       ├── Chat/
│       │   ├── ChatInterface.php
│       │   ├── MessageInput.php
│       │   └── ModelSelector.php
│       ├── Sidebar/
│       │   ├── ConversationList.php
│       │   └── PromptLibrary.php
│       └── Settings/
│           └── UserSettings.php
│
├── Models/
│   ├── User.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── AiProvider.php
│   ├── AiModel.php
│   ├── Favorite.php
│   ├── PromptLibrary.php
│   └── UserSetting.php
│
└── Policies/
    └── ConversationPolicy.php
```

---

## INTERFAZ AIProvider (CONTRATO OBLIGATORIO)

Todo proveedor de IA DEBE implementar esta interfaz:

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

---

## BASE DE DATOS

### Migraciones requeridas (en orden):

1. `users` — Laravel default + campos extra: `avatar`, `default_model`, `credits`
2. `user_settings` — `user_id`, `theme`, `language`, `default_model_id`, `temperature`, `max_tokens`
3. `ai_providers` — `name`, `slug`, `base_url`, `is_active`, `requires_key`
4. `ai_models` — `provider_id`, `name`, `slug`, `context_window`, `is_free`, `is_active`
5. `conversations` — `user_id`, `model_id`, `title`, `temperature`, `total_tokens`, `total_cost`, `status`
6. `messages` — `conversation_id`, `role` (user/assistant/system), `content`, `tokens`, `response_time_ms`, `metadata` (JSON)
7. `favorites` — `user_id`, `conversation_id`
8. `prompt_library` — `user_id`, `title`, `content`, `category`, `is_public`, `use_count`

---

## REGLAS DE CÓDIGO

### Principios SOLID
- **S**: cada clase tiene una sola responsabilidad
- **O**: abierto para extensión (nuevos providers = nueva clase, sin tocar las existentes)
- **L**: los providers son intercambiables via AIProvider interface
- **I**: interfaces pequeñas y específicas
- **D**: inyectar dependencias, nunca instanciar con `new` dentro de servicios

### Controllers
```php
// ✅ CORRECTO
class ChatController extends Controller
{
    public function store(SendMessageRequest $request, ConversationService $service)
    {
        return $service->sendMessage($request->toDTO());
    }
}

// ❌ INCORRECTO — nunca lógica en controllers
class ChatController extends Controller
{
    public function store(Request $request)
    {
        $conversation = Conversation::find($request->id);
        $response = Http::post('https://api.openai.com/...', [...]);
        // ... 50 líneas más
    }
}
```

### DTOs
```php
final class MessageDTO
{
    public function __construct(
        public readonly int $conversationId,
        public readonly string $role,
        public readonly string $content,
        public readonly ?int $tokens = null,
        public readonly ?int $responseTimeMs = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            conversationId: $request->conversation_id,
            role: 'user',
            content: $request->message,
        );
    }
}
```

### Services
```php
// Los services coordinan la lógica, no la contienen toda ellos solos
class ConversationService
{
    public function __construct(
        private AIManager $aiManager,
        private MessageService $messageService,
    ) {}

    public function sendMessage(MessageDTO $dto): AIResponseDTO
    {
        // 1. Guardar mensaje del usuario
        // 2. Obtener proveedor correcto
        // 3. Enviar a la IA
        // 4. Guardar respuesta
        // 5. Retornar DTO
    }
}
```

### Livewire
- Componentes pequeños y enfocados (máximo ~150 líneas)
- Usar `wire:stream` para streaming de respuestas
- Separar lógica en Services, no en el componente
- Usar `#[Locked]` en propiedades que no debe modificar el cliente

---

## DISEÑO UI

### Paleta de colores (modo oscuro)
```css
--bg-primary: #0d0d0d;       /* fondo principal */
--bg-secondary: #161616;     /* sidebar, cards */
--bg-tertiary: #1e1e1e;      /* inputs, hover */
--border: #2a2a2a;
--text-primary: #f0f0f0;
--text-secondary: #888888;
--accent: #7c3aed;           /* violeta CarpIA */
--accent-light: #a78bfa;
--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
```

### Layout principal
```
┌─────────────────────────────────────────────────────┐
│  SIDEBAR (260px fijo)  │  MAIN CONTENT              │
│                        │                            │
│  Logo CarpIA           │  Header (modelo activo)    │
│  ─────────────────     │  ─────────────────────     │
│  + Nueva conversación  │                            │
│  🔍 Buscar             │    Área de chat            │
│  ─────────────────     │    (centrada, max-w-3xl)   │
│  // HISTORIAL          │                            │
│  conversacion 1        │                            │
│  conversacion 2        │  ─────────────────────     │
│  ...                   │  Input fijo inferior       │
│  ─────────────────     │  [textarea] [enviar]       │
│  // BIBLIOTECA         └────────────────────────────┘
│  ─────────────────
│  ⚙ Configuración
│  👤 Perfil
│  🚪 Salir
└────────────────────────
```

### Clases TailwindCSS frecuentes
```html
<!-- Sidebar -->
<aside class="w-64 h-screen bg-[#161616] border-r border-[#2a2a2a] flex flex-col fixed left-0 top-0">

<!-- Chat container -->
<main class="flex-1 ml-64 flex flex-col h-screen bg-[#0d0d0d]">

<!-- Mensajes -->
<div class="flex-1 overflow-y-auto px-4 py-6">
  <div class="max-w-3xl mx-auto space-y-6">

<!-- Input area -->
<div class="border-t border-[#2a2a2a] p-4 bg-[#0d0d0d]">
  <div class="max-w-3xl mx-auto">
```

---

## PROVEEDORES DE IA (GRATUITOS PRIORITARIOS)

| Proveedor     | Modelos destacados gratuitos            | URL base                          |
|---------------|-----------------------------------------|-----------------------------------|
| OpenRouter    | Llama 3, Qwen, Mistral, DeepSeek        | https://openrouter.ai/api/v1      |
| Groq          | Llama 3, Mixtral, Gemma                 | https://api.groq.com/openai/v1    |
| Gemini        | gemini-1.5-flash, gemini-2.0-flash      | https://generativelanguage.googleapis.com |
| Cloudflare AI | Llama, Mistral, Phi                     | https://api.cloudflare.com/client/v4/accounts/{id}/ai |
| HuggingFace   | Varios modelos open source              | https://api-inference.huggingface.co |
| Ollama        | Cualquier modelo local                  | http://localhost:11434/api        |
| Mistral       | mistral-small (free tier)               | https://api.mistral.ai/v1         |
| DeepSeek      | deepseek-chat (free tier)               | https://api.deepseek.com/v1       |

Todos usan formato compatible con OpenAI API (messages array + stream).

---

## FASES DE IMPLEMENTACIÓN

### FASE 1 — Base (actual)
- [ ] Setup Laravel 12 + stack completo
- [ ] Migraciones completas
- [ ] Auth (register, login, logout)
- [ ] Layout principal (sidebar + chat)
- [ ] Primer proveedor funcional: Groq
- [ ] Conversaciones básicas con historial

### FASE 2 — Multi-proveedor
- [ ] AIManager + todos los providers
- [ ] Selector de modelos en el chat
- [ ] Streaming de respuestas (wire:stream)
- [ ] Cambio de modelo mid-conversation

### FASE 3 — Funcionalidades
- [ ] Biblioteca de Prompts
- [ ] Favoritos
- [ ] Búsqueda de conversaciones
- [ ] Exportar conversación

### FASE 4 — SaaS
- [ ] Sistema de créditos
- [ ] Suscripciones
- [ ] Panel de administración
- [ ] Analytics de uso

---

## CONVENCIONES DE NOMBRES

- **Clases PHP**: PascalCase (`ConversationService`)
- **Métodos PHP**: camelCase (`sendMessage`)
- **Columnas DB**: snake_case (`response_time_ms`)
- **Rutas**: kebab-case (`/prompt-library`)
- **Componentes Livewire**: PascalCase en clase, kebab en blade (`<livewire:chat.message-input />`)
- **Variables blade**: camelCase (`$conversationList`)

---

## COMANDOS FRECUENTES

```bash
# Crear componente Livewire
php artisan make:livewire Chat/ChatInterface

# Crear migration
php artisan make:migration create_conversations_table

# Crear model + migration + factory
php artisan make:model Conversation -mf

# Correr migraciones
php artisan migrate

# Limpiar cache
php artisan optimize:clear

# Correr tests
php artisan test
```

---

## NOTAS IMPORTANTES

1. **No hardcodear API keys** — usar `.env` y `config/ai.php`
2. **Siempre validar** antes de enviar a la IA
3. **Rate limiting** en rutas de chat desde el inicio
4. **Soft deletes** en `conversations` y `messages`
5. **Policies** para que usuarios solo vean sus propias conversaciones
6. El **carpincho** (mascota) se usa en: pantalla de bienvenida, estados vacíos, errores amigables
7. Idioma por defecto: **español (Chile)**
8. Zona horaria: **America/Santiago**
