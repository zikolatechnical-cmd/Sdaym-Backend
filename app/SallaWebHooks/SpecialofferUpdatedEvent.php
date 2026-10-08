<?php

namespace App\SallaWebHooks;

use App\Services\Salla\SpecialOfferSyncService;

class SpecialofferUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        return app(SpecialOfferSyncService::class)->sync(
            request()->integer('merchant'),
            request()->input('data', []),
        );
    }
}
