<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Collection;

class CreditService
{
    private const COST_PER_TOKEN = [
        'groq' => 0.0000001,      // $0.10 per 1M tokens
        'gemini' => 0.000000075,  // $0.075 per 1M tokens
        'openrouter' => 0.0000002,
        'cloudflare' => 0.0000001,
        'huggingface' => 0.0000001,
        'mistral' => 0.0000002,
        'deepseek' => 0.00000014,
        'ollama' => 0,
    ];

    public function getBalance(User $user): int
    {
        return $user->credits ?? 0;
    }

    public function deductForUsage(User $user, string $provider, int $tokens, ?int $conversationId = null): ?CreditTransaction
    {
        $costPerToken = self::COST_PER_TOKEN[$provider] ?? 0.0000001;
        $cost = (int) ceil($tokens * $costPerToken * 1000); // Convert to credits (1 credit = $0.001)

        if ($cost <= 0) {
            return null;
        }

        $currentBalance = $this->getBalance($user);

        if ($currentBalance < $cost) {
            return null; // Insufficient credits
        }

        $newBalance = $currentBalance - $cost;

        $user->update(['credits' => $newBalance]);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'usage',
            'amount' => -$cost,
            'balance_after' => $newBalance,
            'description' => "Uso de {$provider} ({$tokens} tokens)",
            'metadata' => [
                'provider' => $provider,
                'tokens' => $tokens,
                'conversation_id' => $conversationId,
                'cost_per_token' => $costPerToken,
            ],
        ]);
    }

    public function addCredits(User $user, int $amount, string $description = 'Recarga de créditos', ?array $metadata = null): CreditTransaction
    {
        $currentBalance = $this->getBalance($user);
        $newBalance = $currentBalance + $amount;

        $user->update(['credits' => $newBalance]);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function adminAdjustment(User $user, int $amount, string $description): CreditTransaction
    {
        $currentBalance = $this->getBalance($user);
        $newBalance = $currentBalance + $amount;

        $user->update(['credits' => $newBalance]);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'admin_adjustment',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => $description,
        ]);
    }

    public function getHistory(User $user, int $limit = 50): Collection
    {
        return CreditTransaction::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getUsageStats(User $user, int $days = 30): array
    {
        $transactions = CreditTransaction::where('user_id', $user->id)
            ->where('type', 'usage')
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $totalUsed = $transactions->sum(fn($t) => abs($t->amount));
        $byProvider = $transactions->groupBy('metadata.provider')
            ->map(fn($t) => $t->sum(fn($tx) => abs($tx->amount)));

        return [
            'total_used' => $totalUsed,
            'by_provider' => $byProvider,
            'transaction_count' => $transactions->count(),
        ];
    }
}
