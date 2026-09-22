<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceTokenRequest;
use App\Models\DeviceToken;
use App\Models\Merchant;
use App\Services\FCM\PushNotify;
use Illuminate\Http\JsonResponse;

class DeviceTokenController extends Controller
{
    public function store(StoreDeviceTokenRequest $request, PushNotify $pushNotify): JsonResponse
    {
        $validated = $request->validated();

        $merchant = Merchant::where('public_key', $validated['store_key'])->firstOrFail();
        $hash = hash('sha256', $validated['fcm_token']);

        $device = DeviceToken::updateOrCreate(
            ['merchant_id' => $merchant->id, 'token_hash' => $hash],
            [
                'token' => $validated['fcm_token'],
                'platform' => $validated['platform'] ?? null,
                'last_seen_at' => now(),
            ],
        );

        $pushNotify->subscribeToTopic($validated['fcm_token'], $merchant->notificationTopic());

        return response()->json([
            'registered' => true,
            'topic' => $merchant->notificationTopic(),
        ], $device->wasRecentlyCreated ? 201 : 200);
    }
}
