<?php

namespace App\AI\Contracts;

use App\DTOs\AIResponseDTO;
use Generator;

interface AIProvider
{
    public function sendMessage(array $messages, array $options = []): AIResponseDTO;

    public function streamMessage(array $messages, array $options = []): Generator;

    public function getAvailableModels(): array;

    public function isAvailable(): bool;

    public function getName(): string;

    public function getSlug(): string;
}
