<?php

namespace App\SallaWebHooks;

use App\Services\FCM\OrderNotificationService;
use App\Services\Salla\OrderSyncService;

class OrderCreatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $order = app(OrderSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []),
        );

        if (! $order->notification_sent_at) {
            $sent = app(OrderNotificationService::class)->send(
                $order,
                'طلب جديد',
                'تم إنشاء طلب جديد في المتجر',
                $this->event,
            );
            if ($sent) {
                $order->update(['notification_sent_at' => now()]);
            }
        }

        return response()->json(['received' => true]);
    }
}
