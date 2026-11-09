<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListOrdersRequest;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(ListOrdersRequest $request, Merchant $merchant): JsonResponse
    {
        abort_unless($request->user('api')->merchant_id === $merchant->id, 403);

        return response()->json($merchant->orders()
            ->latest('salla_created_at')
            ->paginate($request->validated('per_page', 15))
            ->through(fn ($order) => [
                'id' => $order->salla_id,
                'reference_id' => $order->reference_id,
                'status' => $order->status,
                'currency' => $order->currency,
                'payment_method' => $order->payment_method,
                'amounts' => $order->amounts,
                'customer' => $order->customer,
                'items' => $order->items,
                'shipping' => $order->shipping,
                'shipments' => $order->shipments,
                'urls' => $order->urls,
                'status_update' => $order->status_update,
                'created_at' => $order->salla_created_at?->toIso8601String(),
                'updated_at' => $order->salla_updated_at?->toIso8601String(),
            ]));
    }
}
