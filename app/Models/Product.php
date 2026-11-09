<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'merchant_id', 'salla_id', 'name', 'sku', 'type', 'status', 'is_available',
        'show_in_app', 'quantity', 'description', 'main_image', 'price', 'sale_price',
        'regular_price', 'urls', 'images', 'options', 'skus', 'categories', 'brand',
        'tags', 'booking_details', 'raw_data', 'salla_created_at', 'salla_updated_at',
        'notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean', 'show_in_app' => 'boolean', 'quantity' => 'decimal:4',
            'price' => 'array', 'sale_price' => 'array', 'regular_price' => 'array',
            'urls' => 'array', 'images' => 'array', 'options' => 'array', 'skus' => 'array',
            'categories' => 'array', 'brand' => 'array', 'tags' => 'array',
            'booking_details' => 'array', 'raw_data' => 'array',
            'salla_created_at' => 'datetime', 'salla_updated_at' => 'datetime',
            'notification_sent_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
