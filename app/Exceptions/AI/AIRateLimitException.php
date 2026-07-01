<?php

namespace App\Exceptions\AI;

use Throwable;

class AIRateLimitException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "El modelo de {$provider} se encuentra temporalmente ocupado. Por favor intenta nuevamente en unos segundos.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Rate limit excedido: {$message}", $provider, $previous);
    }
}
