<?php

namespace App\Livewire\Chat;

use App\AI\AIManager;
use App\Jobs\GenerateConversationTitle;
use App\Models\AiModel;
use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\MessageService;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatInterface extends Component
{
    public ?Conversation $conversation = null;
    public array $messages = [];
    public bool $isLoading = false;
    public ?int $selectedModelId = null;
    public string $streamedContent = '';

    public function mount(?int $conversationId = null): void
    {
        if ($conversationId) {
            $this->loadConversation($conversationId);
        } else {
            $user = auth()->user();
            $this->selectedModelId = $user->default_model_id;

            if (!$this->selectedModelId) {
                $firstModel = AiModel::with('provider')
                    ->where('is_active', true)
                    ->where('is_free', true)
                    ->first();
                $this->selectedModelId = $firstModel?->id;
            }
        }
    }

    public function loadConversation(int $id): void
    {
        $user = auth()->user();
        $service = app(ConversationService::class);

        $this->conversation = $service->getById($id, $user);

        if ($this->conversation) {
            $this->selectedModelId = $this->conversation->model_id;
            $this->messages = $this->conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->toArray();
        }
    }

    #[On('modelSelected')]
    public function handleModelSelected(int $modelId): void
    {
        $this->selectedModelId = $modelId;

        if ($this->conversation) {
            $conversationService = app(ConversationService::class);
            $conversationService->updateModel($this->conversation, $modelId);
        }
    }

    #[On('submitMessage')]
    public function handleSendMessage(string $content): void
    {
        \Log::info('ChatInterface: submitMessage received', ['content' => $content, 'model_id' => $this->selectedModelId]);
        $this->processMessage($content);
    }

    public function processMessage(string $content): void
    {
        if (empty(trim($content))) {
            return;
        }

        $user = auth()->user();
        $conversationService = app(ConversationService::class);
        $messageService = app(MessageService::class);

        if (!$this->conversation) {
            $this->conversation = $conversationService->create(
                $user,
                $this->selectedModelId,
                mb_substr($content, 0, 50)
            );
        }

        $userMessage = $messageService->storeUserMessage($this->conversation, $content);
        $this->messages[] = $userMessage->toArray();

        $aiMessages = $messageService->formatForAI($this->conversation);

        $this->isLoading = true;
        $this->streamedContent = '';

        try {
            $model = AiModel::with('provider')->find($this->selectedModelId);

            if (!$model) {
                throw new \Exception('No hay modelo seleccionado. Selecciona uno del selector.');
            }

            $manager = app(AIManager::class);
            $provider = $manager->provider($model->provider->slug);

            $response = $provider->sendMessage($aiMessages, [
                'model' => $model->slug,
                'temperature' => $this->conversation->temperature ?? 0.7,
            ]);

            \Log::info('ChatInterface: AI response received', [
                'model' => $model->slug,
                'content_length' => strlen($response->content),
                'tokens' => $response->totalTokens,
            ]);

            $fullContent = $response->content;

            $assistantMessage = $messageService->storeAssistantMessage(
                $this->conversation,
                $fullContent,
                $response->totalTokens,
                $response->responseTimeMs,
                $model->slug
            );

            $this->messages[] = $assistantMessage->toArray();

            if (count($this->messages) <= 2 && str_starts_with($this->conversation->title, 'Nueva conversación')) {
                GenerateConversationTitle::dispatch($this->conversation, $content);
            }

            $this->dispatch('conversationCreated');

        } catch (\Exception $e) {
            \Log::error('ChatInterface: AI error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error: ' . $e->getMessage(),
                'created_at' => now()->toISOString(),
            ];
        }

        $this->isLoading = false;
        $this->dispatch('messageSent');
    }

    public function newConversation(): void
    {
        $this->conversation = null;
        $this->messages = [];
        $this->selectedModelId = auth()->user()->default_model_id;

        if (!$this->selectedModelId) {
            $firstModel = AiModel::where('is_active', true)
                ->where('is_free', true)
                ->first();
            $this->selectedModelId = $firstModel?->id;
        }
    }

    public function render()
    {
        return view('livewire.chat.chat-interface');
    }
}
