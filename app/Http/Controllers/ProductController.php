<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, Merchant $merchant): JsonResponse
    {
        return response()->json($merchant->products()
            ->where('status', 'sale')->where('show_in_app', true)
            ->latest('salla_created_at')
            ->through(fn ($product) => [
                'id' => $product->salla_id,
                'name' => $product->name,
                'sku' => $product->sku,
                'type' => $product->type,
                'description' => $product->description,
                'status' => $product->status,
                'is_available' => $product->is_available,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'regular_price' => $product->regular_price,
                'urls' => $product->urls,
                'main_image' => $product->main_image,
                'images' => $product->images,
                'options' => $product->options,
                'skus' => $product->skus,
                'categories' => $product->categories,
                'brand' => $product->brand,
                'tags' => $product->tags,
                'booking_details' => $product->booking_details,
                'created_at' => $product->salla_created_at?->toIso8601String(),
                'updated_at' => $product->salla_updated_at?->toIso8601String(),
            ]));
    }
}
