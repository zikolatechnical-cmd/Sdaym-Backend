<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\SpecialOfferController;
use App\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/salla', [WebHookController::class, 'sallaHandle'])
    ->name('webhooks.salla');

Route::post('/devices', [DeviceTokenController::class, 'store'])
    ->name('devices.store');

Route::get('/stores/{merchant:public_key}/offers', [SpecialOfferController::class, 'index'])
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