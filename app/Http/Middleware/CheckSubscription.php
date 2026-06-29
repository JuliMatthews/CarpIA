<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_admin) {
            return $next($request);
        }

        if ($user->plan === 'premium') {
            $subscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->exists();
            if ($subscription) {
                return $next($request);
            }
        }

        if ($user->promo_access_until && $user->promo_access_until > now()) {
            return $next($request);
        }

        return redirect()->route('subscription.required');
    }
}
