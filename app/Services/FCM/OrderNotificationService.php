<?php

namespace App\Services\FCM;

use App\Models\DeviceToken;
use App\Models\Order;
use RuntimeException;

class OrderNotificationService
{
    public function send(Order $order, string $title, string $body, string $event): bool
    {
        $customerId = filter_var($order->customer['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($customerId === false) {
            return false;
        }

        $sent = false;
        DeviceToken::where('merchant_id', $order->merchant_id)
            ->where('customer_id', $customerId)
            ->chunkById(500, function ($devices) use ($order, $title, $body, $event, &$sent): void {
                $report = app(PushNotify::class)->sendBulkNotification(
                    $devices->pluck('token')->all(), $title, $body,
                    [
                        'type' => 'order',
                        'order_id' => (string) $order->salla_id,
                        'store_key' => $order->merchant->public_key,
                        'event' => $event,
                    ],
                );

                if ($report->hasFailures()) {
                    throw new RuntimeException('Some order notifications could not be delivered.');
                }

                $sent = true;
            });

        return $sent;
    }
}
