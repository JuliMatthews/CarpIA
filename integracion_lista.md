# Integración Transbank Webpay Plus — Lista para Producción

**Última actualización:** 2026-06-30

---

## Estado Actual

| Item | Estado |
|------|--------|
| SDK instalado | `transbank/transbank-sdk:^5.0` |
| Sandbox probado | ✅ Funcional |
| Formulario de validación (1er intento) | ❌ Rechazado |
| Formulario de validación (2do intento) | ✅ Validación automática aprobada |
| Confirmación de datos por soporte | ⏳ Esperando (24h hábiles) |
| API Key productiva | ⏳ Pendiente |

### Resultado del 2do intento (2026-06-30)

| Prueba | Estado |
|--------|--------|
| Crédito aprobada sin cuotas | ✅ |
| Crédito rechazada sin cuotas | ✅ |
| Crédito aprobada con cuotas | ✅ |
| Débito/prepago aprobada | ✅ |
| Débito/prepago rechazada | ✅ |
| Transacción abortada | ✅ |
| Logo (130x59px) | ✅ |
| RUT del comercio | ✅ |
| Código de comercio productivo | ✅ |
| URL | ✅ |
| Anulaciones parcial (opcional) | ⚠️ No implementadas |
| Anulaciones total (opcional) | ⏳ No implementadas |

> **Nota:** Las anulaciones son opcionales y no afectan la validación.

---

## Próximos pasos

1. ⏳ Esperar correo/llamada de soporte Transbank (ia.carpia.cl@gmail.com / +56972164736)
2. Confirmar datos del comercio
3. Recibir API Key productiva
4. Actualizar `.env` en producción
5. Hacer transacción de prueba de $50 CLP en producción

---

## Credenciales

### Sandbox (integración)
| Campo | Valor |
|-------|-------|
| Commerce Code | `597055555532` |
| API Key | `579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C` |
| Ambiente en `.env` | `TRANSBANK_ENV=integration` |
| Constante SDK | `Options::ENVIRONMENT_INTEGRATION` (= `"TEST"`) |

### Producción (pendiente)
| Campo | Valor |
|-------|-------|
| Commerce Code | `597053087507` |
| API Key | ⏳ Pendiente de Transbank |
| Ambiente en `.env` | `TRANSBANK_ENV=production` |

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

| Ruta | Método | Función | Auth |
|------|--------|---------|------|
| `/checkout/direct` | GET | Checkout directo | ✅ |
| `/checkout/create` | POST | Crear transacción | ✅ |
| `/checkout/return` | GET | Retorno de Webpay | ✅ |
| `/checkout/test` | GET | Generar token de prueba | ❌ |
| `/checkout/test-return` | GET | Retorno de prueba | ❌ |

---

## URL de Retorno Configurada

```
https://carpia.cl/checkout/return
https://carpia.cl/checkout/test-return
```

---

## Fixes Realizados (2026-06-30)

### Fix 1: Environment Mapping
**Problema:** El SDK espera `"TEST"` pero `.env` tenía `"integration"`.

**Solución:** Mapeo en `TransbankService.php`:
```php
$env = config('transbank.environment', 'integration');
$environment = $env === 'production'
    ? Options::ENVIRONMENT_PRODUCTION
    : Options::ENVIRONMENT_INTEGRATION;
```

### Fix 2: commitTransaction card_detail
**Problema:** `$response->getCardDetail()->getCardNumber()` fallaba porque `getCardDetail()` retorna un array PHP, no un objeto.

**Solución:** Usar `$response->getCardDetail()` directamente.

### Fix 3: catch \Exception → catch \Throwable
**Problema:** PHP 8.x distingue entre `Exception` y `Error`. El SDK lanzaba `Error` que no era capturado.

**Solución:** Cambiar `catch (\Exception $e)` por `catch (\Throwable $e)` en `testReturn` y `return`.

### Fix 4: Botón "Ir a Webpay" (POST vs GET)
**Problema:** El botón usaba `<a href>` (GET request), pero Webpay espera un POST con el token.

**Solución:** Cambiar a `<form method="POST">` con `token_ws` como campo oculto.

### Fix 5: .env de producción
**Problema:** Tenía `TRANSBANK_ENV=production` con credenciales sandbox.

**Solución:** Cambiar a `TRANSBANK_ENV=integration` hasta obtener API Key productiva.

---

## Flujo de Validación Completado

### Primer intento (rechazado)
- ❌ Transacciones en estado `INITIALIZED` (tokens pegados sin completar pago en Webpay)
- ❌ Logo de 159px en vez de 130px

### Segundo intento (completado)
1. ✅ **Crédito aprobada sin cuotas** — Visa `4051 8856 0044 6623`
2. ✅ **Crédito rechazada sin cuotas** — Mastercard `5186 0595 5959 0568`
3. ✅ **Crédito aprobada con cuotas** — Visa `4051 8856 0044 6623` (3 cuotas)
4. ✅ **Débito/prepago aprobada** — Redcompra `4051 8842 3993 7763`
5. ✅ **Débito/prepago rechazada** — Redcompra `5186 0085 4123 3829`
6. ✅ **Transacción cancelada** — Anulada por usuario en Webpay
7. ✅ **Anulaciones (refunds)** — No implementadas (opcional)

### Tarjetas de prueba utilizadas:

| Tarjeta | Tipo | Resultado |
|---------|------|-----------|
| `4051 8856 0044 6623` | Visa | ✅ Aprobada |
| `5186 0595 5959 0568` | Mastercard | ❌ Rechazada |
| `4051 8842 3993 7763` | Redcompra (débito) | ✅ Aprobada |
| `5186 0085 4123 3829` | Redcompra (débito) | ❌ Rechazada |

### Auth sandbox:
- RUT: `11.111.111-1`
- Clave: `123`

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

## Cuando Llegue la API Key Productiva

### 1. Actualizar `.env` en producción
```
TRANSBANK_ENV=production
WEBPAY_KEY=597053087507
WEBPAY_SECRET=<API_KEY_QUE_ENVÍE_TRANSBANK>
```

### 2. Limpiar caché
```bash
cd ~/carpia.cl && php artisan config:clear && php artisan cache:clear
```

### 3. Transacción de prueba en producción
Transbank pide al menos una transacción real de $50 CLP para validar producción.

### 4. Actualizar `integracion_lista.md`
Marcar API Key como completada.

---

## Notas

- Las transacciones de prueba en sandbox expiran en 10 minutos
- Las evidencias deben ser de transacciones de los últimos 7 días
- Transbank responde en máx. 24 horas hábiles con la API Key
- El correo de contacto es `ia.carpia.cl@gmail.com`
- Formulario de Transbank: https://www.transbankdevelopers.cl/
- **IMPORTANTE:** Las transacciones deben ser completadas en Webpay (no solo pegar el token)
- **IMPORTANTE:** En producción, verificar que `.env` tenga `TRANSBANK_ENV=production` con credenciales reales
