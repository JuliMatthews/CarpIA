<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionService
{
    public function getPlans(): \Illuminate\Support\Collection
    {
        return Plan::active()->ordered()->get();
    }

    public function getUserSubscription(User $user): ?Subscription
    {
        return Subscription::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->first();
    }

    public function isPremium(User $user): bool
    {
        $subscription = $this->getUserSubscription($user);
        return $subscription && $subscription->isActive() && $subscription->plan->name !== 'free';
    }

    public function subscribe(User $user, Plan $plan, bool $isYearly = false, ?string $paymentMethod = null, ?string $externalId = null): Subscription
    {
        // Cancel any existing active subscription
        $existing = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            $existing->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        $startsAt = Carbon::now();
        $endsAt = $isYearly ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'payment_method' => $paymentMethod,
            'external_id' => $externalId,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_yearly' => $isYearly,
        ]);

        // Add monthly credits
        app(CreditService::class)->addCredits(
            $user,
            $plan->monthly_credits,
            "Créditos del plan {$plan->display_name}"
        );

        // Update user plan
        $user->update(['plan' => $plan->name]);

        return $subscription;
    }

    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Downgrade to free
        $freePlan = Plan::where('name', 'free')->first();
        if ($freePlan) {
            $subscription->user->update(['plan' => 'free']);
        }

        return $subscription->fresh();
    }

    public function checkExpiredSubscriptions(): int
    {
        $expired = Subscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        // Downgrade expired users
        $freePlan = Plan::where('name', 'free')->first();
        if ($freePlan) {
            Subscription::where('status', 'expired')
                ->where('ends_at', '<', now())
                ->each(function ($sub) use ($freePlan) {
                    $sub->user->update(['plan' => 'free']);
                });
        }

        return $expired;
    }
}
