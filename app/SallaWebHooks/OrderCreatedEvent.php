<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\OrderSyncService;

class OrderCreatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $order = app(OrderSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []),
        );

        if (! $order->notification_sent_at) {
            app(PushNotify::class)->sendToTopic(
                $order->merchant->notificationTopic(),
                'طلب جديد',
                'تم إنشاء طلب جديد في المتجر',
                [
                    'type' => 'order',
                    'order_id' => $order->salla_id,
                    'store_key' => $order->merchant->public_key,
                    'event' => $this->event,
                ],
            );
            $order->update(['notification_sent_at' => now()]);
        }

        return response()->json(['received' => true]);
    }
}
