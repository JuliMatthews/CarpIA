<?php

namespace App\Exceptions\AI;

use RuntimeException;
use Throwable;

class AIException extends RuntimeException
{
    protected string $provider;
    protected string $userMessage;
    protected ?Throwable $previous;

    public function __construct(string $message, string $provider, string $userMessage, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->provider = $provider;
        $this->userMessage = $userMessage;
        $this->previous = $previous;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public static function provider(string $provider, string $message, ?Throwable $previous = null): static
    {
        return new static(
            "[{$provider}] {$message}",
            $provider,
            "El proveedor {$provider} temporalmente no está disponible. Por favor selecciona otro modelo e intenta nuevamente.",
            $previous
        );
    }
}
