<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\OrderSyncService;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $order = app(OrderSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), true,
        );

        app(PushNotify::class)->sendToTopic(
            $order->merchant->notificationTopic(),
            'تحديث الطلب',
            'تم تحديث حالة طلب في المتجر',
            [
                'type' => 'order',
                'order_id' => $order->salla_id,
                'store_key' => $order->merchant->public_key,
                'event' => $this->event,
            ],
        );

        return response()->json(['received' => true]);
    }
}
