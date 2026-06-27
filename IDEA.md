# IDEA.md — CarpIA.cl
# Documento maestro de producto

---

## ¿QUÉ ES CARPIA?

CarpIA.cl es una plataforma web chilena que reúne múltiples modelos de Inteligencia Artificial en una única interfaz moderna. El usuario puede conversar con distintas IAs, cambiar de modelo durante la conversación, guardar su historial y acceder a una biblioteca de prompts, todo sin tener que registrarse en múltiples servicios.

**Dominio:** carpia.cl
**País objetivo:** Chile (con proyección a Latinoamérica)
**Idioma:** Español

---

## NOMBRE Y MASCOTA

**CarpIA** = Carpincho + IA

El **carpincho** es la mascota oficial:
- Transmite cercanía, tranquilidad e inteligencia
- Aparece en: pantalla de bienvenida, estados vacíos, errores amigables, onboarding
- Estilo visual: ilustración minimalista, moderna, con acento violeta

---

## PROBLEMA QUE RESUELVE

Hoy existen decenas de modelos de IA de alta calidad disponibles gratuitamente (Groq, Gemini, OpenRouter, etc.), pero cada uno tiene su propia interfaz, su propio registro y su propia experiencia. El usuario que quiere comparar o combinar modelos debe manejar múltiples cuentas y pestañas.

**CarpIA** centraliza todo en un solo lugar.

---

## PROPUESTA DE VALOR

| Característica                          | CarpIA | ChatGPT | Claude | Perplexity |
|-----------------------------------------|--------|---------|--------|------------|
| Múltiples modelos en un chat            | ✅     | ❌      | ❌     | ❌         |
| Gratuito (modelos free)                 | ✅     | ⚠️      | ⚠️     | ⚠️         |
| Interfaz en español                     | ✅     | ⚠️      | ⚠️     | ⚠️         |
| Biblioteca de prompts propia            | ✅     | ❌      | ❌     | ❌         |
| Pensado para Latinoamérica              | ✅     | ❌      | ❌     | ❌         |
| Ollama local integrado                  | ✅     | ❌      | ❌     | ❌         |

---

## PÚBLICO OBJETIVO

**Primario:**
- Estudiantes universitarios chilenos
- Desarrolladores y programadores
- Diseñadores y creativos
- Creadores de contenido digital

**Secundario:**
- Profesionales independientes
- Empresas PyME en Chile/LATAM
- Usuarios que no quieren pagar múltiples suscripciones

---

## FUNCIONALIDADES POR VERSIÓN

### v1.0 — MVP (Base sólida)
- Registro e inicio de sesión
- Chat con al menos 2 proveedores (Groq + Gemini)
- Historial de conversaciones
- Cambio de modelo durante la conversación
- Interfaz modo oscuro (layout completo)
- Perfil de usuario básico

### v1.1 — Enriquecimiento
- Streaming de respuestas en tiempo real
- Biblioteca de prompts (guardar, categorizar, reusar)
- Favoritos
- Búsqueda en historial
- Más proveedores (OpenRouter, DeepSeek, Mistral)
- Soporte Ollama local

### v1.2 — Mejoras UX
- Títulos automáticos para conversaciones (generados por IA)
- Exportar conversación (PDF / Markdown)
- Compartir conversación (link público)
- Soporte de adjuntos (imágenes en modelos que lo permitan)
- Modo claro / oscuro toggle

### v2.0 — SaaS
- Sistema de créditos
- Plan Premium (modelos de pago: GPT-4o, Claude 3.5, etc.)
- Panel de administración
- Analytics de uso por usuario
- API pública de CarpIA
- Suscripciones mensuales

### v3.0 — Ecosistema
- Extensión para Chrome
- Aplicación móvil (PWA primero)
- Sincronización multi-dispositivo
- Espacios de trabajo colaborativos
- Integraciones (Notion, Slack, etc.)

---

## MODELOS DE IA SOPORTADOS

### Gratuitos (prioridad MVP)
| Proveedor  | Modelos                              |
|------------|--------------------------------------|
| Groq       | llama-3.3-70b, mixtral-8x7b, gemma2  |
| Gemini     | gemini-2.0-flash, gemini-1.5-flash   |
| OpenRouter | llama-3, qwen, deepseek-r1 (free)    |
| Cloudflare | llama-3, mistral-7b, phi-2           |
| HuggingFace| Modelos open source varios           |
| Ollama     | Cualquier modelo local del usuario   |
| DeepSeek   | deepseek-chat (free tier)            |
| Mistral    | mistral-small (free tier)            |

### De pago (v2.0+)
- OpenAI (GPT-4o, o1)
- Anthropic (Claude 3.5 Sonnet)
- Google (Gemini 1.5 Pro)
- Cohere
- Perplexity API

---

## ARQUITECTURA DE PROVEEDORES

El sistema usa el patrón **Strategy** para los proveedores de IA:

```
AIManager
  └── resuelve qué AIProvider usar
        └── AIProvider (interfaz)
              ├── GroqProvider
              ├── GeminiProvider
              ├── OpenRouterProvider
              ├── CloudflareProvider
              ├── HuggingFaceProvider
              ├── MistralProvider
              ├── DeepSeekProvider
              └── OllamaProvider
```

**Agregar un nuevo proveedor = crear una nueva clase.** No se toca nada más.

---

## DISEÑO VISUAL

### Inspiración
- **ChatGPT**: layout sidebar + chat centrado
- **Claude**: tipografía limpia, espaciado generoso
- **OllamaChat**: estética oscura con acentos de color, comentarios estilo `// código`
- **Perplexity**: velocidad percibida, resultados inmediatos

### Identidad CarpIA
- **Color principal**: violeta `#7c3aed`
- **Fondo**: negro profundo `#0d0d0d`
- **Tipografía**: Inter (UI) + JetBrains Mono (código)
- **Modo**: oscuro por defecto
- **Estilo**: minimalista, mucho espacio, bordes redondeados suaves
- **Personalidad visual**: el carpincho aporta calidez y humor sin ser infantil

### Referencia de layout (inspirado en OllamaChat)
```
┌──────────────┬──────────────────────────────────┐
│   SIDEBAR    │         ÁREA DE CHAT             │
│              │                                  │
│  // MODELO   │   [Logo + Carpincho welcome]     │
│  Groq Llama  │                                  │
│              │   Sugerencias de uso             │
│  // CONV.    │   Ejemplos de prompts            │
│  + Nueva     │   Modelos disponibles            │
│              │                                  │
│  // HISTORIAL│  ──────────────────────────────  │
│  chat 1      │  [Input de mensaje] [Enviar →]   │
│  chat 2      │                                  │
│              │                                  │
│  ──────────  │                                  │
│  Biblioteca  │                                  │
│  Favoritos   │                                  │
│  ──────────  │                                  │
│  ⚙ Config   │                                  │
│  👤 Perfil   │                                  │
│  🚪 Salir    │                                  │
└──────────────┴──────────────────────────────────┘
```

---

## MONETIZACIÓN

### Fase gratuita (ahora)
- Todos los modelos gratuitos disponibles sin costo
- Límite generoso de mensajes (ej. 100/día)
- Sin tarjeta de crédito

### Plan Premium (v2.0)
- Precio objetivo: $5.990 CLP/mes (~6 USD)
- Acceso a modelos de pago (GPT-4o, Claude 3.5)
- Más créditos de IA
- Sin límite de historial
- Soporte prioritario

### Empresas (v2.0+)
- Precio: $19.990 CLP/mes por equipo
- Multi-usuario
- API propia de CarpIA
- Analytics de equipo

---

## DECISIONES TÉCNICAS CLAVE

| Decisión                     | Elección                  | Razón                                          |
|------------------------------|---------------------------|------------------------------------------------|
| Framework backend            | Laravel 12                | Ecosistema maduro, Livewire integrado          |
| Frontend                     | Blade + Livewire + Alpine | Sin complejidad de SPA, reactivo sin JS pesado |
| Auth                         | Sanctum                   | Simple, robusto, integrado en Laravel          |
| Base de datos                | MySQL 8                   | Estable, ampliamente soportado en hosting CL   |
| Cache / Queue                | Redis                     | Preparado para streaming y jobs async          |
| NO usar                      | React / Vue / Inertia     | Complejidad innecesaria para este proyecto     |
| Streaming                    | wire:stream (Livewire 3)  | Nativo, sin WebSockets propios                 |
| Proveedores IA               | Patrón Strategy           | Agregar modelos sin romper lo existente        |

---

## ESTRUCTURA DE CARPETAS DEL PROYECTO

```
CarpIA/
├── app/
│   ├── AI/
│   │   ├── Contracts/AIProvider.php
│   │   ├── Providers/
│   │   └── AIManager.php
│   ├── DTOs/
│   ├── Services/
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Livewire/
│   └── Policies/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php       ← Layout principal
│       ├── livewire/
│       │   ├── chat/
│       │   ├── sidebar/
│       │   └── settings/
│       └── pages/
│           ├── welcome.blade.php
│           └── chat.blade.php
├── database/
│   ├── migrations/
│   └── seeders/
├── config/
│   └── ai.php                      ← Config de todos los proveedores
├── routes/
│   └── web.php
├── SKILL.md                        ← Este archivo
├── IDEA.md                         ← El que estás leyendo
├── README.md                       ← Setup e instalación
├── PHASES.md                       ← Plan de fases detallado
└── AGENTS.md                       ← Instrucciones para OpenCode
```

---

## REFERENCIAS VISUALES

- OllamaChat (interfaz oscura con comentarios estilo código): https://github.com/ollama/ollama
- ChatGPT: https://chat.openai.com
- Claude: https://claude.ai
- Perplexity: https://perplexity.ai

---

## NOTAS DEL FUNDADOR

- Proyecto desarrollado desde Chile, para Latinoamérica
- Prioridad: experiencia de usuario por encima de cantidad de features
- Crecer con orden: primero la base, luego las funcionalidades
- El carpincho no es un chiste; es la identidad que nos diferencia
- Código limpio desde el día 1; la deuda técnica se paga cara en SaaS
