<?php

namespace App\AI;

use App\Exceptions\AI\AIAuthenticationException;
use App\Exceptions\AI\AIConnectionException;
use App\Exceptions\AI\AIException;
use App\Exceptions\AI\AIInsufficientBalanceException;
use App\Exceptions\AI\AIProviderException;
use App\Exceptions\AI\AIQuotaExceededException;
use App\Exceptions\AI\AIRateLimitException;
use App\Exceptions\AI\AITimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class AIErrorHandler
{
    private const MESSAGES = [
        AIQuotaExceededException::class => 'Este modelo alcanzó temporalmente su límite de uso. Puedes intentar nuevamente en unos minutos o seleccionar otro modelo disponible.',
        AIInsufficientBalanceException::class => 'Este modelo no está disponible temporalmente. Mientras resolvemos el servicio, puedes utilizar cualquiera de los otros modelos disponibles.',
        AIRateLimitException::class => 'Este modelo está recibiendo muchas solicitudes en este momento. Intenta nuevamente en unos segundos.',
        AIAuthenticationException::class => 'Estamos verificando el acceso a este modelo. Por favor intenta con otro modelo disponible.',
        AITimeoutException::class => 'La respuesta del modelo tardó más de lo esperado. Intenta nuevamente en unos momentos.',
        AIConnectionException::class => 'No fue posible establecer comunicación con este modelo en este momento. Intenta nuevamente más tarde.',
        AIProviderException::class => 'El proveedor presentó un inconveniente al procesar tu solicitud. Intenta nuevamente o selecciona otro modelo.',
    ];

    private const CONNECTION_MESSAGES = [
        'cURL error 28' => 'La conexión con el modelo tardó demasiado. Intenta con otro modelo.',
        'cURL error 6' => 'No se pudo resolver la dirección del servidor. Intenta nuevamente.',
        'cURL error 7' => 'No fue posible conectar con el servidor. Verifica tu conexión a internet.',
        'Connection refused' => 'El servidor del modelo rechazó la conexión. Intenta más tarde.',
        'SSL' => 'Error de seguridad al conectar. Intenta nuevamente.',
    ];

    private const REQUEST_MESSAGES = [
        401 => 'Hay un problema temporal de autenticación con este proveedor.',
        402 => 'El servicio de este modelo no está disponible temporalmente.',
        403 => 'El acceso a este modelo está restringido temporalmente.',
        429 => 'Este modelo está temporalmente ocupado. Intenta en unos segundos.',
        500 => 'El proveedor presentó un problema interno.',
        502 => 'El servicio del modelo no está disponible. Intenta más tarde.',
        503 => 'El modelo está en mantenimiento. Intenta con otro modelo.',
    ];

    private const DEFAULT_MESSAGE = 'Ocurrió un inconveniente al obtener una respuesta. Por favor intenta nuevamente o selecciona otro modelo.';

    public static function handle(Throwable $exception): string
    {
        if ($exception instanceof AIException) {
            return self::MESSAGES[get_class($exception)] ?? $exception->getMessage();
        }

        if ($exception instanceof RequestException) {
            return self::handleRequestException($exception);
        }

        if ($exception instanceof ConnectionException) {
            return self::handleConnectionException($exception);
        }

        return self::DEFAULT_MESSAGE;
    }

    private static function handleRequestException(RequestException $exception): string
    {
        $status = $exception->response->status();

        if (isset(self::REQUEST_MESSAGES[$status])) {
            return self::REQUEST_MESSAGES[$status];
        }

        if ($status === 429) {
            $body = $exception->response->json();
            $message = $body['error']['message'] ?? '';

            if (str_contains_ci($message, 'quota') || str_contains_ci($message, 'limit')) {
                return self::MESSAGES[AIQuotaExceededException::class];
            }

            return self::MESSAGES[AIRateLimitException::class];
        }

        if ($status >= 500) {
            return self::REQUEST_MESSAGES[500];
        }

        return self::DEFAULT_MESSAGE;
    }

    private static function handleConnectionException(ConnectionException $exception): string
    {
        $message = $exception->getMessage();

        foreach (self::CONNECTION_MESSAGES as $pattern => $friendlyMessage) {
            if (str_contains_ci($message, $pattern)) {
                return $friendlyMessage;
            }
        }

        return self::MESSAGES[AIConnectionException::class];
    }

    public static function handleWithLog(Throwable $exception, string $provider, string $model, ?int $userId = null): string
    {
        $userMessage = self::handle($exception);

        \Log::error("AI Error: {$provider}/{$model}", [
            'provider' => $provider,
            'model' => $model,
            'user_id' => $userId,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'user_message' => $userMessage,
            'trace' => $exception->getTraceAsString(),
        ]);

        return $userMessage;
    }
}
