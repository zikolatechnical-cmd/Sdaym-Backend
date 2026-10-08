<?php

namespace App\Services\Salla;

use App\Models\Merchant;
use App\Models\SpecialOffer;
use Carbon\Carbon;

class SpecialOfferSyncService
{
    public function sync(int $merchantSallaId, array $data): SpecialOffer
    {
        $merchant = Merchant::where('salla_id', $merchantSallaId)->firstOrFail();

        $offer = SpecialOffer::firstOrNew([
            'merchant_id' => $merchant->id,
            'salla_id' => $data['id'],
        ]);

        foreach (['name', 'message', 'offer_type', 'status', 'buy', 'get'] as $field) {
            if (array_key_exists($field, $data)) {
                $offer->{$field} = $data[$field];
            }
        }

        if (array_key_exists('expiry_date', $data)) {
            $offer->expiry_date = $this->parseDate($data['expiry_date']);
        }

        if (array_key_exists('created_at', $data)) {
            $offer->salla_created_at = $this->parseDate($data['created_at']);
        }

        if (array_key_exists('updated_at', $data)) {
            $offer->salla_updated_at = $this->parseDate($data['updated_at']);
        }

        if (! $offer->exists && ! $offer->name) {
            $offer->name = 'عرض جديد';
        }

        $offer->raw_data = array_replace($offer->raw_data ?? [], $data);
        $offer->save();

        return $offer;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        return $value === null || $value === '' ? null : Carbon::parse($value);
    }
}
