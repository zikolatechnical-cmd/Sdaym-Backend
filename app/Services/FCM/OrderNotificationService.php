<?php

namespace App\Services\FCM;

use App\Models\DeviceToken;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
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

                $retryableFailure = null;
                foreach ($report->failures()->getItems() as $failure) {
                    $device = $devices->first(fn ($device) => $device->token === $failure->target()->value());
                    $invalidToken = $failure->messageTargetWasInvalid() || $failure->messageWasSentToUnknownToken();

                    Log::warning('Order notification delivery failed.', [
                        'order_id' => $order->salla_id,
                        'merchant_id' => $order->merchant_id,
                        'device_id' => $device?->id,
                        'event' => $event,
                        'error_type' => get_class($failure->error()),
                        // Firebase errors can contain the registration token.
                        'error' => str_replace($devices->pluck('token')->all(), '[redacted]', $failure->error()->getMessage()),
                        'invalid_token' => $invalidToken,
                    ]);

                    if ($invalidToken) {
                        $device?->delete();
                    } else {
                        $retryableFailure ??= $failure->error();
                    }
                }

                if ($retryableFailure !== null) {
                    throw new RuntimeException('Order notification delivery failed; see the delivery log for details.', 0, $retryableFailure);
                }

                $sent = $sent || $report->successes()->count() > 0;
            });

        return $sent;
    }
}
