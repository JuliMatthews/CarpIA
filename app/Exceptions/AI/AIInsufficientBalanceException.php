<?php

namespace App\Exceptions\AI;

use Throwable;

class AIInsufficientBalanceException extends AIException
{
    public function __construct(string $message, string $provider, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $provider,
            "El servicio de {$provider} no está disponible temporalmente. Por favor selecciona otro modelo.",
            $previous
        );
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static("[{$provider}] Saldo insuficiente: {$message}", $provider, $previous);
    }
}
