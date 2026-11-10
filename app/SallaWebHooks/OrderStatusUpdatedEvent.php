<?php

namespace App\SallaWebHooks;

use App\Services\FCM\OrderNotificationService;
use App\Services\Salla\OrderSyncService;

class OrderStatusUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $order = app(OrderSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), true,
        );

        app(OrderNotificationService::class)->send(
            $order,
            'تحديث الطلب',
            'تم تحديث حالة طلب في المتجر',
            $this->event,
        );

        return response()->json(['received' => true]);
    }
}
