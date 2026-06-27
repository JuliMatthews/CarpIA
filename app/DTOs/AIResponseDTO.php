<?php

namespace App\DTOs;

final class AIResponseDTO
{
    public function __construct(
        public readonly string $content,
        public readonly string $model,
        public readonly string $provider,
        public readonly ?int $promptTokens = null,
        public readonly ?int $completionTokens = null,
        public readonly ?int $totalTokens = null,
        public readonly ?int $responseTimeMs = null,
        public readonly ?float $cost = null,
    ) {}
}
