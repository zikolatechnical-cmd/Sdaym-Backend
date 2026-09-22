<?php

namespace App\Http\Controllers;

use App\Http\Requests\SallaWebhookRequest;
use App\Jobs\ProcessSallaWebhook;
use App\Models\WebhookReceipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SallaWebhookController extends Controller
{
    public function __invoke(SallaWebhookRequest $request): JsonResponse
    {
        if (! $this->hasValidSignature($request)) {
            return response()->json(['message' => 'Invalid webhook signature.'], 401);
        }

        $payload = $request->validated();

        $fingerprint = hash('sha256', implode('|', [
            $payload['event'],
            (string) $payload['merchant'],
            (string) ($payload['created_at'] ?? ''),
            json_encode($payload['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]));

        $receipt = WebhookReceipt::firstOrCreate(
            ['fingerprint' => $fingerprint],
            [
                'event' => $payload['event'],
                'merchant_salla_id' => $payload['merchant'],
                'payload' => $payload,
            ],
        );

        if ($receipt->wasRecentlyCreated) {
            ProcessSallaWebhook::dispatch($receipt->id);
        }

        return response()->json([
            'received' => true,
            'duplicate' => ! $receipt->wasRecentlyCreated,
        ], 202);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = (string) config('services.salla.webhook_secret');

        if ($secret === '') {
            Log::warning('SALLA_WEBHOOK_SECRET is not configured; webhook signature validation was skipped.');

            return app()->environment(['local', 'testing']);
        }

        $signature = (string) $request->header('X-Salla-Signature');
        $signature = Str::startsWith($signature, 'sha256=') ? Str::after($signature, 'sha256=') : $signature;
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return $signature !== '' && hash_equals($expected, $signature);
    }
}
