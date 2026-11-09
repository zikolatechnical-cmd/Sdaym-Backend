<?php

namespace App\SallaWebHooks;

use App\Services\Salla\OrderSyncService;

class OrderStatusUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        app(OrderSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), true,
        );

        return response()->json(['received' => true]);
    }
}
