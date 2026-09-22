<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\SallaWebhookController;
use App\Http\Controllers\SpecialOfferController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/salla', SallaWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('webhooks.salla');

Route::post('/devices', [DeviceTokenController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('devices.store');

Route::get('/stores/{merchant:public_key}/offers', [SpecialOfferController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('offers.index');

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:api')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});
