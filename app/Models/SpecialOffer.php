<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id', 'salla_id', 'name', 'message', 'expiry_date', 'offer_type',
        'status', 'buy', 'get', 'raw_data', 'salla_created_at', 'salla_updated_at',
        'notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'buy' => 'array',
            'get' => 'array',
            'raw_data' => 'array',
            'expiry_date' => 'datetime',
            'salla_created_at' => 'datetime',
            'salla_updated_at' => 'datetime',
            'notification_sent_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
