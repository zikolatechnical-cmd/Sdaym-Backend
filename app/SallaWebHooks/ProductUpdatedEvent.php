<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\ProductSyncService;

class ProductUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $product = app(ProductSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), request()->input('created_at'),
        );

        app(PushNotify::class)->sendToTopic(
            $product->merchant->notificationTopic(),
            'تحديث المنتج: '.$product->name,
            'تم تحديث بيانات المنتج',
            [
                'type' => 'product', 'product_id' => $product->salla_id,
                'store_key' => $product->merchant->public_key, 'event' => $this->event,
            ],
        );

        return response()->json(['received' => true]);
    }
}
