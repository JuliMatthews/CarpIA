<?php

namespace App\Exceptions\AI;

use Throwable;

class AIQuotaExceededException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "El proveedor {$provider} alcanzó temporalmente su límite de uso. Por favor selecciona otro modelo e intenta más tarde.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Cuota excedida: {$message}", $provider, $previous);
    }
}
