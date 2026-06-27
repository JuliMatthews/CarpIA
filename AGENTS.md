# AGENTS.md — CarpIA.cl
# Instrucciones para OpenCode / Agente IA

---

## LEE ESTO PRIMERO

Antes de escribir cualquier línea de código, lee en este orden:

1. `IDEA.md` — visión completa del producto
2. `SKILL.md` — arquitectura, stack y reglas de código
3. `PHASES.md` — en qué fase estamos y qué sigue
4. Este archivo — cómo trabajar en este proyecto

---

## CONTEXTO DEL PROYECTO

**CarpIA.cl** es una plataforma SaaS chilena que unifica múltiples modelos de IA en una sola interfaz. Piensa en "el ChatGPT de Latinoamérica, pero que soporta todos los modelos gratuitos".

- **Stack**: Laravel 12 + PHP 8.4 + Livewire 3 + AlpineJS + TailwindCSS + MySQL
- **Sin React. Sin Vue. Sin Inertia.**
- **Modo oscuro por defecto**, diseño minimalista inspirado en OllamaChat + Claude
- **Mascota**: carpincho (aparece en estados vacíos, errores, bienvenida)

---

## REGLAS DE TRABAJO

### 1. Trabaja por fases
No implementes todo de golpe. Pregunta en qué fase estamos antes de empezar. Consulta `PHASES.md`.

### 2. Código completo y funcional
No generes stubs, no dejes `// TODO` sin implementar, no pongas `// lógica aquí`. Si algo requiere más contexto, pregunta antes.

### 3. Sigue la arquitectura
```
Controllers → Services → DTOs → Models
Livewire Components → Services (no directo a Models)
AIManager → AIProvider (interfaz) → Provider concreto
```

### 4. Estilo de código
- PHP: PSR-12
- Clases: PascalCase
- Métodos: camelCase
- DB columns: snake_case
- Sin lógica en Controllers
- Sin lógica de negocio en Livewire (solo UI + llamada al Service)

### 5. Base de datos
- Siempre soft deletes en conversations y messages
- Siempre timestamps
- Índices en: `user_id`, `conversation_id`, `model_id`, `created_at`

### 6. Seguridad
- Usar Policies para autorización (`ConversationPolicy`, etc.)
- Validar requests con Form Requests
- Rate limiting en rutas de chat
- Nunca exponer API keys en el frontend

---

## FLUJO DE TRABAJO ESPERADO

Cuando el usuario pida implementar algo, tu flujo es:

```
1. Confirmar en qué fase estamos
2. Leer SKILL.md si tienes dudas de arquitectura
3. Crear/modificar migraciones si aplica
4. Crear Model con relaciones y scopes
5. Crear DTO si se transfieren datos entre capas
6. Crear/modificar Service con la lógica
7. Crear Controller delgado
8. Crear componente Livewire
9. Crear vista Blade con TailwindCSS (modo oscuro)
10. Agregar rutas
11. Agregar tests básicos con Pest
```

---

## ESTRUCTURA DE ARCHIVOS CLAVE

```
app/AI/Contracts/AIProvider.php     ← NUNCA modificar la interfaz sin avisar
app/AI/AIManager.php                ← Aquí se resuelve el proveedor
config/ai.php                       ← Config de todos los proveedores
resources/views/layouts/app.blade.php  ← Layout principal, tocar con cuidado
```

---

## COMPONENTES LIVEWIRE EXISTENTES

*(Actualizar esta lista a medida que se crean)*

| Componente                      | Ruta                              | Función                        |
|---------------------------------|-----------------------------------|--------------------------------|
| `chat.chat-interface`           | `app/Livewire/Chat/`              | Chat principal                 |
| `chat.message-input`            | `app/Livewire/Chat/`              | Input inferior del chat        |
| `chat.model-selector`           | `app/Livewire/Chat/`              | Selector de modelo IA          |
| `sidebar.conversation-list`     | `app/Livewire/Sidebar/`           | Lista del historial            |
| `sidebar.prompt-library`        | `app/Livewire/Sidebar/`           | Biblioteca de prompts          |
| `settings.user-settings`        | `app/Livewire/Settings/`          | Configuración del usuario      |

---

## PALETA DE COLORES (MODO OSCURO)

```
Fondo principal:    #0d0d0d
Sidebar / Cards:    #161616
Inputs / Hover:     #1e1e1e
Bordes:             #2a2a2a
Texto principal:    #f0f0f0
Texto secundario:   #888888
Acento (violeta):   #7c3aed
Acento claro:       #a78bfa
```

---

## MODELOS DE IA — PRIORIDAD DE IMPLEMENTACIÓN

1. **Groq** (primero — más fácil, muy rápido)
2. **Gemini** (segundo — free tier generoso)
3. **OpenRouter** (tercero — acceso a muchos modelos)
4. Resto: Cloudflare, HuggingFace, Mistral, DeepSeek, Ollama

---

## ERRORES COMUNES A EVITAR

❌ No usar `new Service()` dentro de otro Service — inyectar por constructor
❌ No poner lógica de IA en Controllers o en Livewire directamente
❌ No hardcodear modelos de IA — todo viene de la tabla `ai_models`
❌ No mezclar HTML y lógica PHP en Blade (usar componentes)
❌ No crear migraciones sin índices en foreign keys
❌ No olvidar `->middleware('auth')` en rutas protegidas

---

## COMANDOS RÁPIDOS

```bash
php artisan make:livewire NombreComponente
php artisan make:model NombreModelo -mf
php artisan make:service NombreService          # con el package correcto
php artisan migrate:fresh --seed                 # reset completo en desarrollo
php artisan test --filter NombreTest
php artisan optimize:clear
npm run dev
```

---

## FASE ACTUAL

**Ver PHASES.md para saber en qué fase estamos.**

Al iniciar una sesión, pregunta: *"¿En qué tarea de la fase actual trabajamos hoy?"*
