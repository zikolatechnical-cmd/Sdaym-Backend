<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\JsonResponse;

class MerchantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Merchant::orderBy('name')->orderBy('id')->get()
                ->map(fn (Merchant $merchant) => [
                    ...$merchant->toArray(),
                    'store_key' => $merchant->public_key,
                ]),
        ]);
    }
}
