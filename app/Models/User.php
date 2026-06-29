<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'default_model_id',
        'credits',
        'plan',
        'is_admin',
        'promo_access_until',
        'used_share_trial',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'promo_access_until' => 'datetime',
            'used_share_trial' => 'boolean',
            'password' => 'hashed',
            'credits' => 'integer',
            'is_admin' => 'boolean',
        ];
    }

    public function hasActivePromoAccess(): bool
    {
        return $this->promo_access_until && $this->promo_access_until->isFuture();
    }

    public function promoRedemptions(): HasMany
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function promptLibrary(): HasMany
    {
        return $this->hasMany(PromptLibrary::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
