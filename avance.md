# Avance del Proyecto CarpIA.cl — 2026-07-02

## Resumen General

Plataforma SaaS de IA **CarpIA.cl** con chat funcional, integración Transbank Webpay Plus validada, y sistema de proveedores IA refactorizado con manejo de errores robusto.

**Stack Principal:** Laravel 12, PHP 8.4, Livewire 3, AlpineJS, TailwindCSS 4, MySQL.

**Estado Actual:** Fases 1-4 completadas. Transbank validado. Providers IA: Groq y Mistral funcionando.

---

## Estado de Providers IA (Producción — 2026-07-02)

| Provider | Estado | Notas |
|----------|--------|-------|
| **Groq** | ✅ Funciona | Funcionando perfecto |
| **Mistral** | ✅ Funciona | Funcionando perfecto |
| OpenRouter | ❌ Oculto | API key inválida (401) |
| Gemini | ❌ Oculto | Free tier limit: 0 (429) |
| DeepSeek | ❌ Oculto | Sin créditos (402) |
| Cloudflare | ⏭️ Disponible | Sin API key |
| HuggingFace | ⏭️ Disponible | Sin API key |
| Ollama | ⏭️ Disponible | Solo local |

**Orden actual en selector:** Groq → Mistral → Cloudflare → HuggingFace → Ollama

**Para habilitar providers ocultos:** Editar `$excludedProviders` en `app/Livewire/Chat/ModelSelector.php`

---

## Cambios Realizados (2026-07-02)

### 1. Refactorización del Sistema de IA

#### AbstractAIProvider (NUEVO)
- Clase base abstracta con lógica común para todos los providers
- Timeout: 30s (conexión: 10s)
- Reintentos automáticos: 2 intentos
- Headers: Content-Type, Accept, User-Agent (CarpIA/1.0)
- Logging detallado automático
- Manejo centralizado de errores

#### Jerarquía de Excepciones (NUEVO)
```
app/Exceptions/AI/
├── AIException.php              ← Base
├── AIAuthenticationException.php ← 401
├── AIRateLimitException.php      ← 429
├── AIQuotaExceededException.php  ← Cuota excedida
├── AIInsufficientBalanceException.php ← 402
├── AIConnectionException.php     ← Conexión rechazada
├── AITimeoutException.php        ← Timeout
└── AIProviderException.php       ← Errores genéricos
```

#### AIErrorHandler (NUEVO)
- Traduce errores técnicos a mensajes amigables
- Maneja: RequestException, ConnectionException, AIException
- Logging automático con provider, modelo, user_id

### 2. Mensajes Amigables al Usuario

| Error | Mensaje |
|-------|---------|
| Quota exceeded | "Este modelo alcanzó temporalmente su límite de uso. Puedes intentar nuevamente en unos minutos o seleccionar otro modelo disponible." |
| Insufficient Balance | "Este modelo no está disponible temporalmente. Mientras resolvemos el servicio, puedes utilizar cualquiera de los otros modelos disponibles." |
| Rate limit | "Este modelo está recibiendo muchas solicitudes en este momento. Intenta nuevamente en unos segundos." |
| 401 Auth | "Estamos verificando el acceso a este modelo. Por favor intenta con otro modelo disponible." |
| Timeout | "La respuesta del modelo tardó más de lo esperado. Intenta nuevamente en unos momentos." |
| Connection | "No fue posible establecer comunicación con este modelo en este momento. Intenta nuevamente más tarde." |
| Provider error | "El proveedor presentó un inconveniente al procesar tu solicitud. Intenta nuevamente o selecciona otro modelo." |

### 3. System Prompt - Respuesta en Español

- **Archivo:** `config/ai.php` → `system_prompt`
- **Default:** "Eres CarpIA, un asistente de inteligencia artificial. Responde siempre en español de Chile..."
- **Personalizable:** Cada usuario puede configurar su propio prompt en Settings
- **Combinación:** Se concatena el prompt base + el del usuario

### 4. Selector de Modelos - Orden Personalizado

- **Orden:** Groq → Mistral → resto
- **Proveedores ocultos:** openrouter, gemini, deepseek (dan error en producción)
- **Para mostrar:** Cambiar `$excludedProviders` en ModelSelector.php

### 5. Scroll Automático en Chat

- **Al enviar mensaje:** scroll automático
- **Al recibir respuesta:** scroll automático
- **Durante loading:** scroll automático
- **Evento manual:** `scrollToBottom`

### 6. Enter para Enviar Mensaje

- **Enter** → envía el mensaje
- **Shift+Enter** → salto de línea
- Script re-inicializa automáticamente si el textarea se vuelve a renderizar

---

## Archivos Modificados

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `app/AI/AbstractAIProvider.php` | **CREADO** | Clase base abstracta |
| `app/AI/AIErrorHandler.php` | **CREADO** | Traductor de errores |
| `app/Exceptions/AI/*.php` | **CREADOS** | 8 excepciones personalizadas |
| `app/AI/Providers/GroqProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/GeminiProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/OpenRouterProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/MistralProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/DeepSeekProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/OllamaProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/CloudflareProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/AI/Providers/HuggingFaceProvider.php` | MODIFICADO | Extiende AbstractAIProvider |
| `app/Livewire/Chat/ChatInterface.php` | MODIFICADO | AIErrorHandler + scroll |
| `app/Livewire/Chat/ModelSelector.php` | MODIFICADO | Orden + providers ocultos |
| `config/ai.php` | MODIFICADO | system_prompt por defecto |
| `resources/views/livewire/chat/chat-interface.blade.php` | MODIFICADO | Scroll automático |
| `resources/views/livewire/chat/message-input.blade.php` | MODIFICADO | Enter para enviar |
| `bootstrap/app.php` | MODIFICADO | API responses para Livewire |

---

## Transbank Webpay Plus (Production)

- **Commerce Code:** `597053087507`
- **API Key:** `2318ba94-6726-446e-a202-cbd826c334ef`
- **Status:** Validado en sandbox, credenciales productivas configuradas
- **URL retorno:** `https://carpia.cl/checkout/return`
- **SDK:** `transbank/transbank-sdk:^5.0`

---

## Próximos Pasos

### IA Providers
- [ ] Habilitar Generative Language API en Google Cloud Console (Gemini)
- [ ] Probar OpenRouter con API key válida
- [ ] Recargar créditos DeepSeek (opcional)
- [ ] Configurar API keys de Cloudflare y HuggingFace

### Funcionalidades
- [ ] Transacción de prueba $50 CLP en producción Transbank
- [ ] Agregar emails de bienvenida
- [ ] Dashboard de administración
- [ ] Sistema de referidos
- [ ] Notificaciones de expiración de plan

### Mejoras
- [ ] Fallback automático: si falla un provider, usar otro
- [ ] Streaming real con wire:stream
- [ ] Cache de modelos activos

---

## Comandos Útiles

```bash
# Test providers
php artisan test:providers

# Ver logs de providers
tail -200 storage/logs/laravel.log | grep "API response"

# Ver errores
tail -100 storage/logs/laravel.log | grep "ERROR"

# Limpiar caché
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Reconstruir caché
php artisan config:cache && php artisan view:cache

# Actualizar desde GitHub
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
```

---

## Notas para Producción (HostGator)

### Variables de Entorno Importantes
```env
AI_DEFAULT_PROVIDER=groq
AI_SYSTEM_PROMPT=Eres CarpIA... (prompt por defecto)
GROQ_API_KEY=gsk_...
MISTRAL_API_KEY=...
```

### Después de cada deploy
```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
php artisan config:cache && php artisan view:cache
```

### Providers que funcionan en HostGator
- ✅ Groq
- ✅ Mistral
- ❌ OpenRouter (401 - API key inválida)
- ❌ Gemini (429 - quota agotada)
- ❌ DeepSeek (402 - sin créditos)
