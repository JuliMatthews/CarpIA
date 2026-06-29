# Avance del Proyecto CarpIA.cl — 2026-06-29

## Resumen General

Se han implementado funcionalidades clave para la plataforma SaaS de IA **CarpIA.cl**, enfocadas en autenticación con Google, flujo de onboarding, sistema de suscripciones, códigos promocionales, funcionalidad de compartir para obtener acceso temporal (deshabilitada temporalmente) e **integración con Transbank Webpay Plus**.

**Stack Principal:** Laravel 12, PHP 8.4, Livewire 3, AlpineJS, TailwindCSS 4, MySQL.

**Estado Actual:** Fases 1-4 completadas. Integración con Transbank Webpay Plus lista para producción.

---

## Cambios Realizados Hoy (2026-06-29)

### 1. Login solo con Google
- Se eliminó el registro/login tradicional con email y contraseña
- Ahora los usuarios solo pueden autenticarse con Google OAuth
- Se actualizó la vista `auth/login.blade.php` con botón de Google prominente
- Se mantuvo la funcionalidad de Laravel Sanctum para sesiones

### 2. Botón Comenzar en home
- Se agregó botón "Comenzar" en la página de bienvenida (`welcome.blade.php`)
- El botón redirige al dashboard o al chat si el usuario ya está autenticado
- Mejora el flujo de onboarding para nuevos usuarios

### 3. Ruta Dashboard
- Se creó ruta `/dashboard` que muestra una vista resumen para el usuario
- Incluye acceso rápido al chat, información de plan actual y uso de créditos
- Se protegió con middleware `auth`

### 4. Middleware CheckSubscription
- Se creó middleware `CheckSubscription` para verificar estado de suscripción
- Si el usuario no tiene plan activo, se redirige a pantalla de suscripción
- Se aplicó a rutas del chat para limitar acceso según plan

### 5. Pantalla de suscripción en el chat
- Se creó componente Livewire `SubscriptionWall` que se muestra cuando:
  - Usuario no tiene suscripción activa
  - Usuario excedió límites de su plan
- Muestra planes disponibles y opción de upgrade
- Se integra en `ChatInterface` antes de permitir enviar mensajes

### 6. Fix manejo errores GroqProvider
- Se corrigió el manejo de excepciones en `GroqProvider.php`
- Ahora captura errores de rate limit, autenticación y timeouts
- Se mejoró el mensaje de error para el usuario (no expone detalles técnicos)
- Se agregó retry automático en errores temporales

### 7. Plan premium $1.990 CLP
- Se actualizó el plan Premium con precio de **$1.990 CLP/mes**
- Incluye 500.000 tokens mensuales
- Acceso a modelos premium (GPT-4o, Claude, Gemini Pro)
- Se actualizó `PlanSeeder` y migración existente

### 8. Códigos promocionales funcionando
- Se implementó sistema de códigos promo con:
  - Tabla `promo_codes` (code, discount_percent, valid_until, max_uses)
  - Tabla `promo_code_usages` para tracking de usos
  - Servicio `PromoCodeService` con validación y aplicación
  - Componente Livewire `RedeemPromoCode` en settings
- Los códigos pueden dar: descuento porcentual, créditos gratis, o días gratis de premium
- Se crearon códigos de prueba: `CARPIA2026`, `BIENVENIDA100`

### 9. Compartir para obtener 24h gratis (DESHABILITADO)
- Se creó componente Livewire `ShareForAccess` con botones para X, Facebook y WhatsApp
- Se creó vista `share-for-access.blade.php` con botones compactos en fila
- Se creó migración `used_share_trial` en tabla users para control anti-abuso
- **Estado actual:** Los archivos existen pero el componente NO se muestra en la UI
- **Para activar:** Agregar `<livewire:chat.share-for-access />` en `chat-interface.blade.php`

### 10. Integración Transbank Webpay Plus
- Se instaló `transbank/transbank-sdk:^5.0` (SDK oficial de Transbank)
- Se creó `config/transbank.php` con configuración de producción
- Se creó `app/Services/TransbankService.php` - Wrapper del SDK (create + commit)
- Se creó `app/Http/Controllers/PaymentController.php` - Controlador de checkout
- Se creó `app/Livewire/Chat/SubscriptionWall.php` - Componente con botón de pago
- Se creó `resources/views/livewire/chat/subscription-wall.blade.php` - UI del muro
- Se creó `resources/views/checkout/redirect.blade.php` - Formulario auto-submit a Webpay
- Se agregaron rutas `/checkout/create` y `/checkout/return` en `routes/web.php`
- **Estado actual:** Código listo para producción, pendiente obtener API Key de Transbank

---

## Archivos Modificados/Creados Hoy

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `app/Http/Middleware/CheckSubscription.php` | Creado | Verifica suscripción activa |
| `app/Livewire/Chat/SubscriptionWall.php` | Creado | Pantalla de suscripción en chat |
| `app/Livewire/Chat/ShareForAccess.php` | Creado | Sistema share para 24h gratis (deshabilitado) |
| `app/Livewire/Settings/RedeemPromoCode.php` | Creado | Canje de códigos promo |
| `app/Services/PromoCodeService.php` | Creado | Lógica de códigos promocionales |
| `app/Models/PromoCode.php` | Creado | Modelo de código promo |
| `app/Models/PromoCodeUsage.php` | Creado | Tracking de usos |
| `app/Models/User.php` | Modificado | Agregado `used_share_trial` a fillable y casts |
| `database/migrations/xxxx_create_promo_codes_table.php` | Creado | Migración tabla códigos |
| `database/migrations/xxxx_create_promo_code_usages_table.php` | Creado | Migración tracking usos |
| `database/migrations/2026_06_29_000000_add_used_share_trial_to_users_table.php` | Creado | Campo control share trial |
| `database/seeders/PromoCodeSeeder.php` | Creado | Códigos de prueba |
| `resources/views/auth/login.blade.php` | Modificado | Login solo Google |
| `resources/views/welcome.blade.php` | Modificado | Botón Comenzar |
| `resources/views/dashboard.blade.php` | Creado | Vista dashboard |
| `resources/views/livewire/chat/subscription-wall.blade.php` | Creado | Vista suscripción |
| `resources/views/livewire/chat/share-for-access.blade.php` | Creado | Vista share buttons (deshabilitado) |
| `resources/views/livewire/settings/redeem-promo-code.blade.php` | Creado | Vista canje código |
| `app/AI/Providers/GroqProvider.php` | Modificado | Fix manejo errores |
| `database/seeders/PlanSeeder.php` | Modificado | Plan Premium $1.990 |
| `routes/web.php` | Modificado | Rutas dashboard + checkout |
| `bootstrap/app.php` | Modificado | Registro middleware |
| `config/transbank.php` | Creado | Configuración Transbank |
| `app/Services/TransbankService.php` | Creado | Wrapper SDK Transbank |
| `app/Http/Controllers/PaymentController.php` | Creado | Controlador checkout |
| `app/Livewire/Chat/SubscriptionWall.php` | Modificado | Agregado botón Webpay |
| `resources/views/livewire/chat/subscription-wall.blade.php` | Modificado | UI con planes y pago |
| `resources/views/checkout/redirect.blade.php` | Creado | Auto-submit a Webpay |
| `.env` | Modificado | Credenciales Transbank |

---

## Para Activar "Compartir para 24h gratis"

Cuando se quiera habilitar, solo ejecutar:

```bash
# 1. Ejecutar migración pendiente
php artisan migrate

# 2. Agregar componente en chat-interface.blade.php
# Después del formulario de código promo, agregar:
<livewire:chat.share-for-access />
```

---

## Próximos Pasos

### URGENTE - Integración Transbank
- [x] Instalar SDK `transbank/transbank-sdk:^5.0`
- [x] Crear `TransbankService` wrapper del SDK
- [x] Crear `PaymentController` con create + return
- [x] Crear `SubscriptionWall` con botón de pago
- [x] Agregar rutas de checkout
- [x] Configurar `.env` con credenciales (producción)
- [ ] **Obtener API Key de Transbank** (llamar a soporte: 600 638 6380)
- [ ] Actualizar `WEBPAY_SECRET` en `.env` con API Key real
- [ ] Probar flujo completo de pago en producción
- [ ] Configurar URL de retorno en portal de Transbank

### Pendientes
- [ ] Agregar emails de bienvenida y confirmación de suscripción
- [ ] Dashboard de administración: ver códigos promo usados
- [ ] Sistema de referidos (código único por usuario)
- [ ] Notificaciones de expiración de plan
- [ ] Testing completo del flujo de suscripción
- [ ] Crear redes sociales de CarpIA (X, Facebook, WhatsApp)
- [ ] Activar funcionalidad de share cuando las redes estén listas

---

## Estado de la Base de Datos

```bash
# Para sincronizar cambios de hoy:
php artisan migrate:fresh --seed

# Para probar códigos promo:
# En la BD inserts: CARPIA2026 (20% dto), BIENVENIDA100 (100% gratis 7 días)
```

---

## Notas Técnicas

- El middleware `CheckSubscription` se registra en `bootstrap/app.php` como `check.subscription`
- Los códigos promo validan: uso máximo, fecha de expiración, y si ya fue usado por el usuario
- GroqProvider ahora retorna `AIResponseDTO` incluso en errores con mensaje amigable
- El precio del plan Premium ($1.990 CLP) está en `plans` table, no hardcodeado
- El componente `ShareForAccess` verifica `used_share_trial` para garantizar uso único
- Los links de sharing usan URLs oficiales de cada red (no necesitan cuentas de CarpIA)

### Transbank Webpay Plus
- **Código de comercio producción:** 53087507
- **API Key:** Pendiente - obtener de Transbank (no visible en portal)
- **URL retorno producción:** `https://carpia.cl/checkout/return`
- **SDK instalado:** `transbank/transbank-sdk:^5.0`
- **Flujo:** POST /checkout/create → Redirect Webpay → GET /checkout/return → Confirmar
- **Seguridad:** Nunca se guardan datos de tarjeta, todo maneja Transbank
- **Documentación:** https://transbankdevelopers.cl/documentacion/webpay-plus