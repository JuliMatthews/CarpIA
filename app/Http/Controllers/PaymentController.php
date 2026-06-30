<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\TransbankService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private TransbankService $transbankService,
        private SubscriptionService $subscriptionService
    ) {}

    public function direct(Request $request)
    {
        $plan = Plan::where('name', 'premium')->firstOrFail();

        $user = $request->user();
        $buyOrder = strtoupper(substr('CARPIA' . Str::ulid(), 0, 26));
        $sessionId = $user->id . '-' . Str::random(10);
        $amount = $plan->monthly_price;
        $returnUrl = route('checkout.return');

        $result = $this->transbankService->createTransaction(
            $buyOrder,
            $sessionId,
            (int) $amount,
            $returnUrl
        );

        session([
            'pending_subscription' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'is_yearly' => false,
                'buy_order' => $buyOrder,
                'amount' => $amount,
                'token' => $result['token'],
            ],
        ]);

        return view('checkout.redirect', [
            'url' => $result['url'],
            'token' => $result['token'],
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'is_yearly' => 'boolean',
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->name === 'free') {
            return redirect()->route('planes')
                ->with('error', 'El plan gratuito no requiere pago.');
        }

        $buyOrder = strtoupper(substr('CARPIA' . Str::ulid(), 0, 26));
        $sessionId = $user->id . '-' . Str::random(10);
        $amount = $request->is_yearly ? $plan->yearly_price : $plan->monthly_price;
        $returnUrl = route('checkout.return');

        $result = $this->transbankService->createTransaction(
            $buyOrder,
            $sessionId,
            (int) $amount,
            $returnUrl
        );

        // Store pending subscription in session
        session([
            'pending_subscription' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'is_yearly' => $request->is_yearly ?? false,
                'buy_order' => $buyOrder,
                'amount' => $amount,
                'token' => $result['token'],
            ],
        ]);

        // Return view with auto-submit form for Webpay
        return view('checkout.redirect', [
            'url' => $result['url'],
            'token' => $result['token'],
        ]);
    }

    public function test(Request $request)
    {
        $buyOrder = 'CARPIATEST-' . strtoupper(Str::random(10));
        $sessionId = 'test-' . Str::random(10);
        $amount = 1990;
        $returnUrl = route('checkout.test-return');

        $result = $this->transbankService->createTransaction(
            $buyOrder,
            $sessionId,
            $amount,
            $returnUrl
        );

        session([
            'test_transaction' => [
                'buy_order' => $buyOrder,
                'session_id' => $sessionId,
                'amount' => $amount,
                'token' => $result['token'],
                'created_at' => now()->toIso8601String(),
            ],
        ]);

        return view('checkout.test', [
            'token' => $result['token'],
            'url' => $result['url'],
            'buyOrder' => $buyOrder,
            'amount' => $amount,
        ]);
    }

    public function testReturn(Request $request)
    {
        $token = $request->input('token_ws') ?? $request->input('TBK_TOKEN');
        $testTx = session('test_transaction');

        if (!$token) {
            return view('checkout.test-result', [
                'success' => false,
                'message' => 'No se recibió token de la transacción.',
                'token' => null,
                'status' => null,
                'details' => null,
                'testTx' => $testTx,
            ]);
        }

        try {
            $result = $this->transbankService->commitTransaction($token);

            $isApproved = $result['status'] === 'AUTHORIZED';

            return view('checkout.test-result', [
                'success' => $isApproved,
                'message' => $isApproved
                    ? 'Transacción APROBADA correctamente.'
                    : 'Transacción RECHAZADA (esto es esperado para tarjetas de prueba de rechazo).',
                'token' => $token,
                'status' => $result['status'],
                'details' => $result,
                'testTx' => $testTx,
            ]);

        } catch (\Throwable $e) {
            return view('checkout.test-result', [
                'success' => false,
                'message' => 'Error al confirmar la transacción: ' . $e->getMessage(),
                'token' => $token,
                'status' => 'ERROR',
                'details' => null,
                'testTx' => $testTx,
            ]);
        }
    }

    public function return(Request $request)
    {
        $token = $request->input('token_ws') ?? $request->input('TBK_TOKEN');
        $pending = session('pending_subscription');

        if (!$token || !$pending) {
            return redirect()->route('planes')
                ->with('error', 'No se encontró la transacción. Intenta nuevamente.');
        }

        try {
            $result = $this->transbankService->commitTransaction($token);

            if ($result['status'] === 'AUTHORIZED') {
                $user = \App\Models\User::find($pending['user_id']);
                $plan = Plan::find($pending['plan_id']);

                $this->subscriptionService->subscribe(
                    user: $user,
                    plan: $plan,
                    isYearly: $pending['is_yearly'],
                    paymentMethod: 'webpay',
                    externalId: $result['authorization_code']
                );

                session()->forget('pending_subscription');

                return redirect()->route('chat')
                    ->with('success', '¡Suscripción activada! Ya puedes usar todos los modelos.');
            }

            return redirect()->route('planes')
                ->with('error', 'El pago no fue aprobado. Intenta nuevamente.');

        } catch (\Throwable $e) {
            \Log::error('Webpay commit error: ' . $e->getMessage());

            return redirect()->route('planes')
                ->with('error', 'Error al procesar el pago. Intenta nuevamente.');
        }
    }
}
