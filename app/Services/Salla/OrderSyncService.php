<?php

namespace App\Services\Salla;

use App\Models\Merchant;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderSyncService
{
    public function sync(int $merchantSallaId, array $data, bool $statusUpdate = false): Order
    {
        $orderData = $statusUpdate ? ($data['order'] ?? []) : $data;
        Validator::make($orderData, ['id' => ['required', 'integer', 'min:1']])->validate();

        $merchant = Merchant::where('salla_id', $merchantSallaId)->firstOrFail();
        $order = Order::firstOrNew([
            'merchant_id' => $merchant->id,
            'salla_id' => $orderData['id'],
        ]);

        foreach (['reference_id', 'status', 'currency', 'payment_method', 'amounts',
            'customer', 'items', 'shipping', 'shipments', 'urls'] as $field) {
            if (array_key_exists($field, $orderData)) {
                $order->{$field} = $orderData[$field];
            }
        }

        if (array_key_exists('date', $orderData)) {
            $order->salla_created_at = $this->parseDate($orderData['date']);
        }

        if ($statusUpdate) {
            $order->status_update = array_diff_key($data, ['order' => true]);
            if (! array_key_exists('status', $orderData) && isset($data['status'])) {
                $order->status = ['name' => $data['status'], 'customized' => $data['customized'] ?? null];
            }
            if (array_key_exists('created_at', $data)) {
                $order->salla_updated_at = $this->parseDate($data['created_at']);
            }
        } elseif (array_key_exists('updated_at', $orderData)) {
            $order->salla_updated_at = $this->parseDate($orderData['updated_at']);
        }

        $order->raw_data = array_replace($order->raw_data ?? [], $orderData);
        $order->save();

        return $order;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (is_array($value)) {
            return empty($value['date']) ? null : Carbon::parse($value['date'], $value['timezone'] ?? 'UTC')->utc();
        }

        return $value === null || $value === '' ? null : Carbon::parse($value)->utc();
    }
}
