<?php

namespace App\SallaWebHooks;

use App\Services\FCM\PushNotify;
use App\Services\Salla\SpecialOfferSyncService;

class SpecialofferCreatedEvent extends EventHandlerManager
{
    public function process(): mixed
    {
        $offer = app(SpecialOfferSyncService::class)->sync(
            request()->integer('merchant'),
            request()->input('data', []),
        );

        if (! $offer->notification_sent_at) {
            $merchant = $offer->merchant;

            app(PushNotify::class)->sendToTopic(
                $merchant->notificationTopic(),
                'عرض جديد: '.$offer->name,
                $offer->message ?: 'اكتشف العرض الجديد الآن',
                [
                    'type' => 'special_offer',
                    'offer_id' => $offer->salla_id,
                    'store_key' => $merchant->public_key,
                    'event' => $this->event,
                ],
            );

            $offer->update(['notification_sent_at' => now()]);
        }

        return $offer;
    }
}
