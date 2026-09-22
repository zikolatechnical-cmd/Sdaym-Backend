<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Merchant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'public_key', 'salla_id', 'name', 'username', 'email', 'mobile', 'avatar',
        'domain', 'plan', 'commercial_number', 'tax_number', 'access_token',
        'refresh_token', 'token_type', 'scopes', 'token_expires_at', 'installed_at',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'scopes' => 'array',
            'token_expires_at' => 'datetime',
            'installed_at' => 'datetime',
        ];
    }

    public function specialOffers(): HasMany
    {
        return $this->hasMany(SpecialOffer::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function notificationTopic(): string
    {
        return 'merchant_'.$this->salla_id.'_offers';
    }
}
