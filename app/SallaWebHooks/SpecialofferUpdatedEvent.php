<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\SpecialOfferSyncService;

class SpecialofferUpdatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $offer = app(SpecialOfferSyncService::class)->sync(
            request()->integer('merchant'),
            request()->input('data', []),
        );

        app(PushNotify::class)->sendToTopic(
            $offer->merchant->notificationTopic(),
            'تحديث العرض: '.$offer->name,
            $offer->message ?: 'تم تحديث تفاصيل العرض',
            [
                'type' => 'special_offer',
                'offer_id' => $offer->salla_id,
                'store_key' => $offer->merchant->public_key,
                'event' => $this->event,
            ],
        );

        return $offer;
    }
}
