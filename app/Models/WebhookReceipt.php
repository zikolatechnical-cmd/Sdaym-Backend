<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookReceipt extends Model
{
    protected $fillable = ['fingerprint', 'event', 'merchant_salla_id', 'payload', 'processed_at', 'failed_at', 'error'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'processed_at' => 'datetime', 'failed_at' => 'datetime'];
    }
}
