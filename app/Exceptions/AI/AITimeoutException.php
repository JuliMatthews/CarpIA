<?php

namespace App\Exceptions\AI;

use Throwable;

class AITimeoutException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "El proveedor {$provider} tardó demasiado en responder. Por favor intenta nuevamente o selecciona otro modelo.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Timeout: {$message}", $provider, $previous);
    }
}
