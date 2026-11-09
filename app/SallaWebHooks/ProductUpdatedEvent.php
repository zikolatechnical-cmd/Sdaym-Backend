<?php

namespace App\SallaWebHooks;

use App\Services\Salla\ProductSyncService;

class ProductUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        app(ProductSyncService::class)->sync(
            request()->integer('merchant'), request()->input('data', []), request()->input('created_at'),
        );

        return response()->json(['received' => true]);
    }
}
