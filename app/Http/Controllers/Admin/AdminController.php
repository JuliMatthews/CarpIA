<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Plan;
use App\Models\PromoCode;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'total_subscriptions' => Subscription::active()->count(),
            'premium_users' => User::where('plan', '!=', 'free')->count(),
            'total_credits_used' => CreditTransaction::where('type', 'usage')->sum(\DB::raw('ABS(amount)')),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentSubscriptions = Subscription::with(['user', 'plan'])->latest()->limit(10)->get();

        $messagesByDay = Message::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $modelUsage = Message::where('role', 'assistant')
            ->whereNotNull('metadata->model')
            ->selectRaw('metadata->>"$.model" as model, COUNT(*) as count')
            ->groupBy('model')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentSubscriptions',
            'messagesByDay',
            'modelUsage'
        ));
    }

    public function users()
    {
        $users = User::with(['subscription.plan'])
            ->withCount('conversations')
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function user(User $user)
    {
        $user->load([
            'subscription.plan',
            'creditTransactions' => fn($q) => $q->latest()->limit(20),
            'conversations' => fn($q) => $q->with(['messages' => fn($q) => $q->latest()->limit(5)])
                ->withCount('messages')
                ->latest()
                ->limit(20),
        ]);

        $stats = [
            'total_conversations' => $user->conversations()->count(),
            'total_messages' => Message::whereIn('conversation_id', $user->conversations()->pluck('id'))->count(),
            'credits_used' => CreditTransaction::where('user_id', $user->id)->where('type', 'usage')->sum(\DB::raw('ABS(amount)')),
        ];

        return view('admin.user', compact('user', 'stats'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'credits' => 'required|integer|min:0',
            'plan' => 'required|string|in:free,premium,pro',
            'is_admin' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.user', $user)->with('success', 'Usuario actualizado.');
    }

    public function promoCodes()
    {
        $codes = PromoCode::withCount('redemptions')->latest()->paginate(20);

        return view('admin.promo-codes', compact('codes'));
    }

    public function storePromoCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'description' => 'nullable|string|max:500',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'duration_hours' => 'required|integer|min:1|max:720',
        ]);

        $validated['created_by'] = auth()->id();

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes')->with('success', 'Código promocional creado.');
    }

    public function togglePromoCode(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);

        return redirect()->route('admin.promo-codes')->with('success', 'Estado del código actualizado.');
    }

    public function models()
    {
        $models = \App\Models\AiModel::with('provider')->get();
        $providers = \App\Models\AiProvider::all();

        return view('admin.models', compact('models', 'providers'));
    }

    public function toggleModel(\App\Models\AiModel $model)
    {
        $model->update(['is_active' => !$model->is_active]);

        return redirect()->route('admin.models')->with('success', 'Modelo actualizado.');
    }

    public function plans()
    {
        $plans = Plan::withCount('subscriptions')->get();

        return view('admin.plans', compact('plans'));
    }
}
