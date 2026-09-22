<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListSpecialOffersRequest;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;

class SpecialOfferController extends Controller
{
    public function index(ListSpecialOffersRequest $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validated();

        $offers = $merchant->specialOffers()
            ->latest('salla_created_at')
            ->paginate($validated['per_page'] ?? 15)
            ->through(fn ($offer) => [
                'id' => $offer->salla_id,
                'name' => $offer->name,
                'message' => $offer->message,
                'expiry_date' => $offer->expiry_date?->toIso8601String(),
                'offer_type' => $offer->offer_type,
                'status' => $offer->status,
                'buy' => $offer->buy,
                'get' => $offer->get,
                'created_at' => $offer->salla_created_at?->toIso8601String(),
                'updated_at' => $offer->salla_updated_at?->toIso8601String(),
            ]);

        return response()->json($offers);
    }
}
