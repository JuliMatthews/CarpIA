<?php

namespace App\Exceptions\AI;

use Throwable;

class AIAuthenticationException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "Hay un problema temporal de autenticación con {$provider}. Nuestro equipo ya fue notificado.",
            $previous
        );
    }

    public static function missing(string $provider): static
    {
        return new static(
            "[{$provider}] API key no configurada",
            $provider
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Error de autenticación: {$message}", $provider, $previous);
    }
}
