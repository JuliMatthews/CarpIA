# Integración Transbank Webpay Plus — Lista para Producción

**Fecha:** 2026-06-30

---

## Estado Actual

| Item | Estado |
|------|--------|
| SDK instalado | `transbank/transbank-sdk:^5.0` |
| Sandbox probado | ✅ Funcional |
| Formulario de validación | ✅ Completado |
| API Key productiva | ⏳ Pendiente (24h hábiles) |

---

## Credenciales

### Sandbox (integración)
| Campo | Valor |
|-------|-------|
| Commerce Code | `597055555532` |
| API Key | `579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C` |
| Ambiente | `integration` |

### Producción (pendiente)
| Campo | Valor |
|-------|-------|
| Commerce Code | `597053087507` |
| API Key | ⏳ Pendiente de Transbank |
| Ambiente | `production` |

---

## Archivos Clave

| Archivo | Función |
|---------|---------|
| `app/Services/TransbankService.php` | Wrapper del SDK (create + commit) |
| `app/Http/Controllers/PaymentController.php` | Controlador de checkout (direct, create, return, test, testReturn) |
| `app/Livewire/Chat/SubscriptionWall.php` | Componente con planes y botón de pago |
| `config/transbank.php` | Configuración de Transbank |
| `resources/views/checkout/redirect.blade.php` | Auto-submit a Webpay |
| `resources/views/checkout/test.blade.php` | Página de prueba para generar tokens |
| `resources/views/checkout/test-result.blade.php` | Resultado de la transacción de prueba |

---

## Rutas

| Ruta | Método | Función |
|------|--------|---------|
| `/checkout/direct` | GET | Checkout directo (requiere auth) |
| `/checkout/create` | POST | Crear transacción (requiere auth) |
| `/checkout/return` | GET | Retorno de Webpay (requiere auth) |
| `/checkout/test` | GET | Generar token de prueba (sin auth) |
| `/checkout/test-return` | GET | Retorno de prueba |

---

## URL de Retorno Configurada

```
https://carpia.cl/checkout/return
https://carpia.cl/checkout/test-return
```

---

## Flujo de Validación Completado

### Pruebas realizadas en el formulario de Transbank:

1. ✅ **Transacción aprobada** — Tarjeta crédito, sin cuotas
2. ✅ **Transacción rechazada** — Tarjeta crédito, sin cuotas
3. ✅ **Transacción aprobada con cuotas** — Tarjeta crédito, con cuotas
4. ✅ **Transacción con tarjeta débito/prepago** — Aprobada
5. ✅ **Transacción cancelada** — Anulada por el usuario en Webpay
6. ✅ **Anulaciones (refunds)** — No implementadas (opcional)

### Tarjetas de prueba utilizadas:

| Tarjeta | Tipo | Resultado |
|---------|------|-----------|
| `4051 8856 0044 6623` | Visa | ✅ Aprobada |
| `5186 0595 5959 0568` | Mastercard | ❌ Rechazada |
| `4051 8860 0005 6590` | Prepago Visa | ✅ Aprobada |

### Auth sandbox:
- RUT: `11.111.111-1`
- Clave: `123`

---

## Fix Importante: Botón "Ir a Webpay"

**Problema:** El botón original usaba `<a href>` (GET request), pero Webpay espera un POST con el token.

**Solución:** Cambiar a `<form method="POST">` con `token_ws` como campo oculto.

```html
<!-- ANTES (no funcionaba) -->
<a href="{{ $url }}">Ir a Webpay</a>

<!-- DESPUÉS (funciona) -->
<form action="{{ $url }}" method="POST">
    <input type="hidden" name="token_ws" value="{{ $token }}" />
    <button type="submit">Ir a Webpay</button>
</form>
```

---

## Cuando Llegue la API Key Productiva

### 1. Actualizar `.env` en producción

```bash
# En el servidor SSH
nano ~/carpia.cl/.env
```

Cambiar:
```
TRANSBANK_ENV=production
WEBPAY_KEY=597053087507
WEBPAY_SECRET=<API_KEY_QUE_ENVÍE_TRANSBANK>
```

### 2. Actualizar `config/transbank.php`

Verificar que el ambiente se lea correctamente de `.env`:
```php
'environment' => env('TRANSBANK_ENV', 'integration'),
```

### 3. Limpiar caché

```bash
cd ~/carpia.cl && php artisan config:clear && php artisan cache:clear
```

### 4. Transacción de prueba en producción

Transbank pide al menos una transacción real de $50 CLP para validar producción.

---

## Datos del Formulario de Validación

| Campo | Valor |
|-------|-------|
| Código de comercio | `597053087507` |
| RUT | `26820665-5` |
| Nombre | Julio Matheus |
| Correo | `ia.carpia.cl@gmail.com` |
| Teléfono | `9 7216 4736` |
| PSP | No |
| Integrador | No |
| Tipo de desarrollo | SDK Oficial Transbank |
| Lenguaje | PHP |
| Producto | Webpay Plus |
| Moneda | Pesos Chilenos (CLP) |
| URL sitio | `https://carpia.cl/` |
| URL producción | `https://carpia.cl/` |

---

## Notas

- Las transacciones de prueba en sandbox expiran en 10 minutos
- Las evidencias deben ser de transacciones de los últimos 7 días
- Transbank responde en máx. 24 horas hábiles con la API Key
- El correo de contacto es `ia.carpia.cl@gmail.com`
- Formulario de Transbank: https://www.transbankdevelopers.cl/
