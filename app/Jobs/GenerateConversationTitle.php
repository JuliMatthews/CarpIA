<?php

namespace App\Jobs;

use App\AI\AIManager;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateConversationTitle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public string $firstMessage,
    ) {
        $this->onQueue('ai');
    }

    public function handle(AIManager $manager, ConversationService $service): void
    {
        try {
            $provider = $manager->provider('groq');

            $response = $provider->sendMessage([
                ['role' => 'system', 'content' => 'Genera un título muy corto (máximo 50 caracteres) para esta conversación. Solo el título, sin comillas ni explicaciones. El título debe ser en español.'],
                ['role' => 'user', 'content' => $this->firstMessage],
            ], [
                'model' => 'llama-3.1-8b-instant',
                'temperature' => 0.3,
                'max_tokens' => 60,
            ]);

            $title = trim($response->content, "\"' \n\r\t.");

            if (!empty($title) && mb_strlen($title) <= 80) {
                $service->updateTitle($this->conversation, $title);
            }
        } catch (\Exception $e) {
            // Fallback: use first words of the message
            $title = mb_substr($this->firstMessage, 0, 50);
            if (mb_strlen($this->firstMessage) > 50) {
                $title .= '...';
            }
            $service->updateTitle($this->conversation, $title);
        }
    }
}
