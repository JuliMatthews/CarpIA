<?php

namespace App\Exceptions\AI;

use Throwable;

class AIConnectionException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "No fue posible conectar con {$provider}. Por favor verifica tu conexión e intenta nuevamente.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Error de conexión: {$message}", $provider, $previous);
    }
}
