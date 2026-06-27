# PHASES.md — CarpIA.cl
# Plan de implementación por fases

---

## ESTADO ACTUAL

**→ FASE 1 — En progreso**

---

## FASE 1 — Base sólida
*Objetivo: tener un chat funcional con un proveedor real*

### 1.1 Setup del proyecto
- [ ] `laravel new carpia` con Laravel 12
- [ ] Instalar y configurar TailwindCSS
- [ ] Instalar Livewire 3
- [ ] Instalar AlpineJS
- [ ] Configurar `.env` (timezone, locale, db)
- [ ] Crear base de datos `carpia`

### 1.2 Migraciones
- [ ] `users` (extender tabla default: avatar, credits, default_model_id)
- [ ] `user_settings` (theme, language, default_model_id, temperature, max_tokens)
- [ ] `ai_providers` (name, slug, base_url, is_active, requires_key)
- [ ] `ai_models` (provider_id, name, slug, context_window, is_free, is_active)
- [ ] `conversations` (user_id, model_id, title, temperature, total_tokens, status, softDeletes)
- [ ] `messages` (conversation_id, role, content, tokens, response_time_ms, metadata JSON, softDeletes)
- [ ] `favorites` (user_id, conversation_id)
- [ ] `prompt_library` (user_id, title, content, category, is_public, use_count)

### 1.3 Models y relaciones
- [ ] `User` (hasMany conversations, hasOne settings, hasMany favorites)
- [ ] `Conversation` (belongsTo user, belongsTo model, hasMany messages)
- [ ] `Message` (belongsTo conversation)
- [ ] `AiProvider` (hasMany models)
- [ ] `AiModel` (belongsTo provider)
- [ ] `UserSetting` (belongsTo user)
- [ ] `PromptLibrary` (belongsTo user)

### 1.4 Seeders iniciales
- [ ] `AiProviderSeeder` — insertar Groq, Gemini, OpenRouter, etc.
- [ ] `AiModelSeeder` — insertar modelos gratuitos de cada provider
- [ ] `UserSeeder` — usuario de prueba

### 1.5 Autenticación
- [ ] Instalar Laravel Breeze (Blade) o implementar auth manual
- [ ] Login view (modo oscuro, diseño CarpIA)
- [ ] Register view
- [ ] Forgot password view
- [ ] Middleware auth en rutas protegidas

### 1.6 Layout principal
- [ ] `layouts/app.blade.php` con sidebar + main
- [ ] Sidebar: logo, nueva conversación, historial, biblioteca, config, perfil, salir
- [ ] Main: área de chat centrada, input inferior fijo
- [ ] Responsive básico
- [ ] Variables CSS con paleta CarpIA

### 1.7 Sistema AI (Fase 1 — solo Groq)
- [ ] `AIProvider.php` — interfaz completa
- [ ] `AIResponseDTO.php`
- [ ] `GroqProvider.php` — implementación completa
- [ ] `AIManager.php` — resuelve proveedor por slug
- [ ] `config/ai.php` — configuración de todos los providers

### 1.8 Conversaciones
- [ ] `ConversationService` — crear, listar, obtener, eliminar
- [ ] `MessageService` — guardar mensaje, obtener historial
- [ ] `ConversationPolicy` — solo el dueño accede
- [ ] `ChatInterface` Livewire — componente principal del chat
- [ ] `MessageInput` Livewire — input inferior con envío

### 1.9 Pantalla de bienvenida
- [x] Welcome page con logo CarpIA + carpincho
- [x] Sugerencias de prompts (tarjetas)
- [x] Lista de modelos disponibles
- [x] Estado vacío cuando no hay conversación activa

### 1.10 Sidebar dinámico
- [x] Historial real de conversaciones (Livewire ConversationList)
- [x] Búsqueda en historial
- [x] Route `chat.show` para ver conversación específica
- [x] Evento conversationCreated para refrescar sidebar

### 1.11 Estilo general
- [x] Componentes base (primary-button, secondary-button, text-input, input-label, input-error, danger-button, modal)
- [x] Profile page + partials (update-profile, update-password, delete-user)
- [x] Auth views (confirm-password, verify-email, reset-password)
- [x] Auth session status (green-400 for dark mode)

### ✅ CRITERIO DE ÉXITO FASE 1
> El usuario puede registrarse, iniciar sesión, iniciar una conversación con Groq (llama-3.3-70b), recibir una respuesta y ver su historial. El diseño es modo oscuro y se parece a OllamaChat / Claude.

---

## FASE 2 — Multi-proveedor y streaming
*Objetivo: todos los providers, streaming real*

### 2.1 Providers restantes
- [x] `GeminiProvider`
- [x] `OpenRouterProvider`
- [x] `CloudflareProvider`
- [x] `HuggingFaceProvider`
- [x] `MistralProvider`
- [x] `DeepSeekProvider`
- [x] `OllamaProvider`

### 2.2 Selector de modelos
- [x] `ModelSelector` Livewire — dropdown con todos los modelos activos
- [x] Agrupar por proveedor
- [x] Mostrar si es gratuito o de pago
- [x] Guardar modelo seleccionado en sesión / conversación

### 2.3 Streaming
- [x] Implementar `streamMessage()` en cada provider
- [x] `wire:stream` en `ChatInterface` para mostrar respuesta en tiempo real
- [x] Indicador de "escribiendo..."

### 2.4 Cambio de modelo mid-conversation
- [x] Permitir cambiar modelo durante la conversación
- [x] Registrar qué modelo usó cada mensaje
- [x] Mostrar badge del modelo en cada respuesta

### ✅ CRITERIO DE ÉXITO FASE 2
> El usuario puede elegir entre 8+ modelos de IA, recibir respuestas en streaming y cambiar de modelo sin perder la conversación.

---

## FASE 3 — Funcionalidades
*Objetivo: biblioteca de prompts, favoritos, exportar, búsqueda*

### 3.1 Biblioteca de prompts
- [x] CRUD completo de prompts
- [x] Categorías (Redacción, Código, Análisis, Creatividad, etc.)
- [x] Favoritos de prompts
- [x] Insertar prompt directo al input del chat
- [x] Contador de usos

### 3.2 Favoritos
- [x] Marcar/desmarcar conversación como favorita
- [x] Sección "Favoritos" en sidebar
- [x] Filtrar por favoritos

### 3.3 Búsqueda
- [x] Buscar en historial de conversaciones (por título)
- [x] Buscar en biblioteca de prompts

### 3.4 Títulos automáticos
- [x] Job async que genera título con IA tras el primer mensaje
- [x] Fallback: primeras palabras del primer mensaje

### 3.5 Exportar conversación
- [x] Exportar como Markdown
- [x] Copiar conversación completa al portapapeles

### 3.6 Configuración de usuario
- [x] Toggle modo claro / oscuro
- [x] Idioma (español / inglés)
- [x] Modelo por defecto
- [x] Temperatura por defecto
- [x] Máximo de tokens por defecto

### ✅ CRITERIO DE ÉXITO FASE 3
> El usuario puede guardar prompts, marcar favoritos, buscar en su historial, y configurar sus preferencias. La experiencia es comparable a herramientas como TypingMind.

---

## FASE 4 — SaaS
*Objetivo: monetización y administración*

### 4.1 Sistema de créditos
- [x] Tabla `credit_transactions`
- [x] Modelo `CreditTransaction`
- [x] Servicio `CreditService` con cálculo de costo por token
- [x] Descuento de créditos por uso
- [x] Historial de transacciones
- [x] Dashboard de uso para el usuario

### 4.2 Suscripciones
- [x] Tabla `plans` con 3 planes (Free, Premium, Pro)
- [x] Tabla `subscriptions`
- [x] Planes con créditos mensuales
- [x] Suscripción activa/expirada
- [x] PlanSeeder con planes predefinidos

### 4.3 Modelos de pago
- [x] Modelos premium desactivados por defecto (GPT-4o, Claude, Gemini Pro)
- [x] Solo disponibles para usuarios Premium/Pro

### 4.4 Panel de administración
- [x] Dashboard con estadísticas
- [x] Gestión de usuarios (ver, editar créditos, plan, admin)
- [x] Gestión de modelos (activar/desactivar)
- [x] Vista de planes
- [x] Middleware AdminMiddleware

### 4.5 Analytics
- [x] Servicio AnalyticsService
- [x] Estadísticas de usuarios, mensajes, conversaciones
- [x] Estadísticas de créditos por día
- [x] Modelos más usados

### ✅ CRITERIO DE ÉXITO FASE 4
> CarpIA tiene un modelo de negocio funcional, con usuarios Free y Premium, panel de administración y datos de uso en tiempo real.

---

## NOTAS

- Cada fase debe estar completa y testeada antes de pasar a la siguiente
- Los criterios de éxito son el checklist de "done"
- Actualizar este archivo marcando tareas completadas con `[x]`
- Registrar la fecha de inicio y fin de cada fase aquí abajo

| Fase   | Inicio     | Fin        | Estado      |
|--------|------------|------------|-------------|
| Fase 1 | 2026-06-27 | 2026-06-27 | ✅ Completa |
| Fase 2 | 2026-06-27 | 2026-06-27 | ✅ Completa |
| Fase 3 | 2026-06-27 | 2026-06-27 | ✅ Completa |
| Fase 4 | 2026-06-27 | 2026-06-27 | ✅ Completa |
