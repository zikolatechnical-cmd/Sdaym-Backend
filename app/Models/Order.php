<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'merchant_id', 'salla_id', 'reference_id', 'status', 'currency',
        'payment_method', 'amounts', 'customer', 'items', 'shipping', 'shipments',
        'urls', 'status_update', 'raw_data', 'salla_created_at',
        'salla_updated_at', 'notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'array', 'amounts' => 'array', 'customer' => 'array',
            'items' => 'array', 'shipping' => 'array', 'shipments' => 'array',
            'urls' => 'array', 'status_update' => 'array', 'raw_data' => 'array',
            'salla_created_at' => 'datetime', 'salla_updated_at' => 'datetime',
            'notification_sent_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
