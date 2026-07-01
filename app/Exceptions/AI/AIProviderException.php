<?php

namespace App\Exceptions\AI;

use Throwable;

class AIProviderException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "El proveedor {$provider} respondió con un error inesperado. Por favor selecciona otro modelo e intenta nuevamente.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Error del proveedor: {$message}", $provider, $previous);
    }

    public static function forbidden(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static(
            "[{$provider}] Acceso restringido: {$message}",
            $provider,
            $previous
        );
    }

    public static function serverError(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static(
            "[{$provider}] Error interno del servidor: {$message}",
            $provider,
            $previous
        );
    }
}
