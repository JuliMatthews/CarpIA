<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\CreditTransaction;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardStats(): array
    {
        return [
            'users' => $this->getUserStats(),
            'messages' => $this->getMessageStats(),
            'conversations' => $this->getConversationStats(),
            'credits' => $this->getCreditStats(),
            'models' => $this->getModelStats(),
        ];
    }

    public function getUserStats(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);

        return [
            'total' => User::count(),
            'new_by_day' => User::where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'active' => User::where('last_login_at', '>=', $start)->count(),
            'premium' => User::where('plan', '!=', 'free')->count(),
        ];
    }

    public function getMessageStats(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);

        return [
            'total' => Message::count(),
            'by_day' => Message::where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'user_messages' => Message::where('role', 'user')->count(),
            'assistant_messages' => Message::where('role', 'assistant')->count(),
        ];
    }

    public function getConversationStats(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);

        return [
            'total' => Conversation::count(),
            'by_day' => Conversation::where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'active_users' => Conversation::where('created_at', '>=', $start)
                ->distinct('user_id')
                ->count('user_id'),
        ];
    }

    public function getCreditStats(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);

        $transactions = CreditTransaction::where('created_at', '>=', $start);

        return [
            'total_used' => (clone $transactions)->where('type', 'usage')->sum(DB::raw('ABS(amount)')),
            'total_purchased' => (clone $transactions)->where('type', 'purchase')->sum('amount'),
            'by_day' => (clone $transactions)
                ->selectRaw('DATE(created_at) as date, SUM(ABS(amount)) as total')
                ->where('type', 'usage')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    public function getModelStats(): array
    {
        return Message::where('role', 'assistant')
            ->whereNotNull('metadata->model')
            ->selectRaw('metadata->>"$.model" as model, COUNT(*) as count')
            ->groupBy('model')
            ->orderByDesc('count')
            ->get();
    }

    public function getUserUsage(int $userId, int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);
        $conversations = Conversation::where('user_id', $userId)->pluck('id');

        return [
            'messages' => Message::whereIn('conversation_id', $conversations)
                ->where('created_at', '>=', $start)
                ->count(),
            'conversations' => Conversation::where('user_id', $userId)
                ->where('created_at', '>=', $start)
                ->count(),
            'credits_used' => CreditTransaction::where('user_id', $userId)
                ->where('type', 'usage')
                ->where('created_at', '>=', $start)
                ->sum(DB::raw('ABS(amount)')),
        ];
    }
}
