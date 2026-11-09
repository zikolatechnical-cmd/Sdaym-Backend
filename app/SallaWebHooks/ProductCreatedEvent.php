<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\ProductSyncService;

class ProductCreatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $product = app(ProductSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), request()->input('created_at'),
        );

        if (! $product->notification_sent_at) {
            app(PushNotify::class)->sendToTopic(
                $product->merchant->notificationTopic(),
                'منتج جديد: '.$product->name,
                'اكتشف المنتج الجديد الآن',
                [
                    'type' => 'product', 'product_id' => $product->salla_id,
                    'store_key' => $product->merchant->public_key, 'event' => $this->event,
                ],
            );
            $product->update(['notification_sent_at' => now()]);
        }

        return response()->json(['received' => true]);
    }
}
