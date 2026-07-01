# Avance del Proyecto CarpIA.cl — 2026-07-01

## Resumen General

Plataforma SaaS de IA **CarpIA.cl** con chat funcional, integración Transbank Webpay Plus validada, y sistema de proveedores IA en producción.

**Stack Principal:** Laravel 12, PHP 8.4, Livewire 3, AlpineJS, TailwindCSS 4, MySQL.

**Estado Actual:** Fases 1-4 completadas. Transbank validado. Providers IA: solo Mistral funciona desde HostGator.

---

## Estado de Providers IA (Producción — 2026-07-01)

| Provider | Estado | HTTP | Problema |
|----------|--------|------|----------|
| **Mistral** | ✅ Funciona | 200 | — |
| Groq | ❌ | 403 | HostGator bloquea IP |
| Gemini | ❌ | 429 | Free tier tiene limit: 0 (API no habilitada en Google Cloud) |
| OpenRouter | ⚠️ | 429 | Rate limit temporal upstream (retry_after: 3s) |
| DeepSeek | ❌ | 402 | Sin créditos en cuenta |
| Cloudflare | ⏭️ | — | Sin API key |
| HuggingFace | ⏭️ | — | Sin API key |
| Ollama | ⏭️ | — | No disponible en server |

**Default provider cambiado a `mistral`** en `config/ai.php`.

### Acciones pendientes para habilitar más providers:
1. **Gemini**: Habilitar "Generative Language API" en Google Cloud Console
2. **OpenRouter**: Reintentar (rate limit temporal de 3s) o probar otros modelos free
3. **DeepSeek**: Recargar créditos en la cuenta
4. **Groq**: No funciona desde hosting compartido (HostGator bloquea IPs)

---

## Cambios Realizados Hoy (2026-07-01)

### 1. Error Handling en todos los Providers
- Todos los providers ahora lanzan `RuntimeException` con mensajes claros
- Antes devolvían contenido vacío silenciosamente
- ChatInterface ya captura `\Exception` y muestra el error al usuario

### 2. Logging detallado en providers
- Cada provider ahora loguea: URL, modelo, HTTP status, elapsed_ms, response_body
- Logs en `storage/logs/laravel.log`
- Filtrar con: `tail -200 storage/logs/laravel.log | grep "API response"`

### 3. Comando `php artisan test:providers`
- Testea cada proveedor con su primer modelo
- Muestra ✅/❌/⏭️ por provider
- Guarda logs detallados para debugging

### 4. Eliminar conversaciones desde el historial
- Botón de papelera al hover en cada conversación
- Modal de confirmación con AlpineJS
- Soft delete (no se borran permanentemente)
- Listener `conversationDeleted` recarga la lista automáticamente

### 5. Default provider cambiado a Mistral
- `config/ai.php`: default era `groq`, ahora es `mistral`
- Mistral es el único que funciona actualmente desde HostGator

### 6. Assets compilados en repo
- `.gitignore` modificado para incluir `public/build/`
- Necesario porque el server no tiene Node.js

---

## Archivos Modificados Hoy

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `app/AI/Providers/GroqProvider.php` | Modificado | Error handling + logging |
| `app/AI/Providers/GeminiProvider.php` | Modificado | Error handling + logging |
| `app/AI/Providers/OpenRouterProvider.php` | Modificado | Error handling + logging |
| `app/AI/Providers/DeepSeekProvider.php` | Modificado | Error handling + logging |
| `app/AI/Providers/MistralProvider.php` | Modificado | Error handling + logging |
| `app/AI/Providers/CloudflareProvider.php` | Modificado | Error handling |
| `app/AI/Providers/HuggingFaceProvider.php` | Modificado | Error handling |
| `app/Console/Commands/TestProviders.php` | Creado | Comando de testing |
| `app/Livewire/Sidebar/ConversationList.php` | Modificado | deleteConversation() |
| `resources/views/livewire/sidebar/conversation-list.blade.php` | Modificado | Botón eliminar + modal |
| `config/ai.php` | Modificado | Default provider → mistral |
| `.gitignore` | Modificado | Incluir public/build |

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
- [ ] Probar OpenRouter con retry o modelos diferentes
- [ ] Recargar créditos DeepSeek (opcional)
- [ ] Evaluar si Mistral free tier es suficiente para producción

### Pendientes
- [ ] Transacción de prueba $50 CLP en producción Transbank
- [ ] Agregar emails de bienvenida
- [ ] Dashboard de administración
- [ ] Sistema de referidos
- [ ] Notificaciones de expiración de plan

---

## Comandos Útiles

```bash
# Test providers
php artisan test:providers

# Ver logs de providers
tail -200 storage/logs/laravel.log | grep "API response"

# Limpiar caché
php artisan config:clear && php artisan cache:clear && php artisan route:clear
```
