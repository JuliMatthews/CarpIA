<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Models\User;

class PromoCodeService
{
    public function redeem(string $code, User $user): array
    {
        $promo = PromoCode::where('code', $code)->first();

        if (!$promo) {
            return ['success' => false, 'message' => 'Código promocional no válido.'];
        }

        if (!$promo->isValid()) {
            return ['success' => false, 'message' => 'Este código ha expirado o ya no está disponible.'];
        }

        $alreadyRedeemed = PromoCodeRedemption::where('promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRedeemed) {
            return ['success' => false, 'message' => 'Ya has canjeado este código anteriormente.'];
        }

        if ($user->hasActivePromoAccess()) {
            return ['success' => false, 'message' => 'Ya tienes acceso activo por un código promocional.'];
        }

        $grantedUntil = now()->addHours($promo->duration_hours);

        $user->update(['promo_access_until' => $grantedUntil]);

        PromoCodeRedemption::create([
            'promo_code_id' => $promo->id,
            'user_id' => $user->id,
            'granted_until' => $grantedUntil,
        ]);

        $promo->increment('use_count');

        return [
            'success' => true,
            'message' => "¡Código canjeado con éxito! Tienes acceso hasta " . $grantedUntil->format('d/m/Y H:i') . ".",
            'granted_until' => $grantedUntil,
        ];
    }
}
