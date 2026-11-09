<?php

namespace App\Services\Salla;

use App\Models\Merchant;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProductSyncService
{
    public function sync(int $merchantSallaId, array $data, mixed $eventDate = null): Product
    {
        Validator::make($data, [
            'id' => ['required', 'integer', 'min:1'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ])->validate();

        $merchant = Merchant::where('salla_id', $merchantSallaId)->firstOrFail();
        $product = Product::firstOrNew(['merchant_id' => $merchant->id, 'salla_id' => $data['id']]);

        foreach (['name', 'sku', 'type', 'status', 'is_available', 'show_in_app',
            'quantity', 'description', 'main_image', 'price', 'sale_price',
            'regular_price', 'urls', 'images', 'options', 'skus', 'categories',
            'brand', 'tags', 'booking_details'] as $field) {
            if (array_key_exists($field, $data)) {
                $product->{$field} = $data[$field];
            }
        }

        if (! $product->exists) {
            $product->name ??= 'منتج جديد';
            $product->salla_created_at = $this->parseDate($data['created_at'] ?? $eventDate);
        }
        if (array_key_exists('updated_at', $data)) {
            $product->salla_updated_at = $this->parseDate($data['updated_at']);
        }

        $product->raw_data = array_replace($product->raw_data ?? [], $data);
        $product->save();

        return $product;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (is_array($value)) {
            return empty($value['date']) ? null : Carbon::parse($value['date'], $value['timezone'] ?? 'Asia/Riyadh')->utc();
        }

        return $value === null || $value === '' ? null : Carbon::parse($value, 'Asia/Riyadh')->utc();
    }
}
