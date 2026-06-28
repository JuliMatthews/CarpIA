# AGENTS.md — CarpIA.cl
# Instrucciones para OpenCode / Agente IA

---

## LEE ESTO PRIMERO

Antes de escribir cualquier línea de código, lee en este orden:

1. `IDEA.md` — visión completa del producto
2. `SKILL.md` — arquitectura, stack y reglas de código
3. `PHASES.md` — en qué fase estamos y qué sigue
4. `avance.md` — último estado real del proyecto
5. Este archivo — cómo trabajar en este proyecto

---

## CONFIGURACIÓN DEL AGENTE

- **Modelo**: `google/gemini-2-5-flash`
- **Working directory**: `C:\laragon\www\Carpia`
- **OS**: Windows 11 con Laragon
- **PHP**: 8.4 | **Laravel**: 12 | **Node**: via NVM

---

## ESTADO ACTUAL DE LA SESIÓN

- **Fases completadas**: Fase 1, 2, 3 y 4 — proyecto base funcional
- **Próxima tarea**: [ACTUALIZAR ESTO AL INICIO DE CADA SESIÓN]
- **Último cambio**: Ver `avance.md` para el historial reciente

---

## CONTEXTO DEL PROYECTO

**CarpIA.cl** es una plataforma SaaS chilena que unifica múltiples modelos de IA en una sola interfaz. Piensa en "el ChatGPT de Latinoamérica, pero que soporta todos los modelos gratuitos".

- **Stack**: Laravel 12 + PHP 8.4 + Livewire 3 + AlpineJS + TailwindCSS + MySQL
- **Sin React. Sin Vue. Sin Inertia.**
- **Modo oscuro por defecto**, diseño minimalista inspirado en OllamaChat + Claude
- **Mascota**: carpincho (aparece en estados vacíos, errores, bienvenida)

---

## REGLAS DE TRABAJO

### 1. Lee antes de editar
Antes de modificar cualquier archivo, léelo completo y muestra las líneas relevantes. Nunca edites a ciegas.

### 2. Trabaja por tareas atómicas
Una tarea a la vez. Confirma que funcionó antes de pasar a la siguiente.

### 3. Código completo y funcional
No generes stubs, no dejes `// TODO` sin implementar, no pongas `// lógica aquí`. Si algo requiere más contexto, pregunta antes.

### 4. Sigue la arquitectura

### 5. Estilo de código
- PHP: PSR-12
- Clases: PascalCase
- Métodos: camelCase
- DB columns: snake_case
- Sin lógica en Controllers
- Sin lógica de negocio en Livewire (solo UI + llamada al Service)

### 6. Base de datos
- Siempre soft deletes en conversations y messages
- Siempre timestamps
- Índices en: `user_id`, `conversation_id`, `model_id`, `created_at`

### 7. Seguridad
- Usar Policies para autorización (`ConversationPolicy`, etc.)
- Validar requests con Form Requests
- Rate limiting en rutas de chat
- Nunca exponer API keys en el frontend

---

## FLUJO DE TRABAJO ESPERADO

Cuando el usuario pida implementar algo, tu flujo es:
Leer avance.md para entender el estado actual
Leer el archivo relevante antes de tocarlo
Mostrar las líneas que se van a modificar
Ejecutar el cambio
Confirmar qué se hizo y qué sigue


Para features nuevas:

Crear/modificar migraciones si aplica
Crear Model con relaciones y scopes
Crear DTO si se transfieren datos entre capas
Crear/modificar Service con la lógica
Crear Controller delgado
Crear componente Livewire
Crear vista Blade con TailwindCSS (modo oscuro)
Agregar rutas
Agregar tests básicos con Pest


---

## ESTRUCTURA DE ARCHIVOS CLAVE
app/AI/Contracts/AIProvider.php        ← NUNCA modificar la interfaz sin avisar

app/AI/AIManager.php                   ← Aquí se resuelve el proveedor

config/ai.php                          ← Config de todos los proveedores

resources/views/layouts/app.blade.php  ← Layout principal, tocar con cuidado

avance.md                              ← Actualizar después de cada sesión

---

## COMPONENTES LIVEWIRE EXISTENTES

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
Fondo principal:    #0d0d0d

Sidebar / Cards:    #161616

Inputs / Hover:     #1e1e1e

Bordes:             #2a2a2a

Texto principal:    #f0f0f0

Texto secundario:   #888888

Acento (violeta):   #7c3aed

Acento claro:       #a78bfa

---

## ERRORES COMUNES A EVITAR

❌ No usar `new Service()` dentro de otro Service — inyectar por constructor
❌ No poner lógica de IA en Controllers o en Livewire directamente
❌ No hardcodear modelos de IA — todo viene de la tabla `ai_models`
❌ No mezclar HTML y lógica PHP en Blade (usar componentes)
❌ No crear migraciones sin índices en foreign keys
❌ No olvidar `->middleware('auth')` en rutas protegidas
❌ No editar archivos sin leerlos primero
❌ No hacer múltiples cambios a la vez sin confirmar cada uno

---

## COMANDOS RÁPIDOS

```bash
php artisan make:livewire NombreComponente
php artisan make:model NombreModelo -mf
php artisan make:service NombreService
php artisan migrate:fresh --seed
php artisan test --filter NombreTest
php artisan optimize:clear
npm run dev
```

---

## FASE ACTUAL

Todas las fases (1-4) están marcadas como completas en `PHASES.md`.
El proyecto tiene chat funcional, multi-proveedor, biblioteca de prompts,
favoritos, sistema de créditos y panel de administración.

**Al iniciar sesión NO preguntes en qué fase estamos.**
Lee `avance.md` y pregunta: *"¿Qué mejoramos o escalamos hoy?"*