<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->stateless()
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    public function callbackToken(Request $request): JsonResponse|RedirectResponse
    {
        $credential = $request->input('credential');

        if (!$credential) {
            return response()->json(['error' => 'No credential provided'], 400);
        }

        // Verify the ID token with Google
        $response = Http::post('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $credential,
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        $payload = $response->json();

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? $payload['email'],
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        $user->update(['last_login_at' => now()]);

        Auth::login($user, remember: true);

        return response()->json(['redirect' => route('chat')]);
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Error al autenticar con Google. Intenta de nuevo.']);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        $user->update(['last_login_at' => now()]);

        Auth::login($user, remember: true);

        return redirect()->intended(route('chat'));
    }
}
