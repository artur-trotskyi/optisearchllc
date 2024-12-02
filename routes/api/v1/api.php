<?php

use App\Enums\Auth\AuthDriverEnum;
use App\Http\Controllers\Api\V1\LoggerController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PriceSubscriptionController;
use Illuminate\Support\Facades\Route;

$authMiddleware = (config('auth.auth_driver') === AuthDriverEnum::SANCTUM->message()) ? 'auth:sanctum' : 'auth:api';
Route::middleware([$authMiddleware])->group(function () {
    Route::apiResource('orders', OrderController::class);

    Route::prefix('loggers')->as('loggers.')->group(function () {
        Route::post('/log', [LoggerController::class, 'log'])->name('default');
        Route::post('/log-to/{type}', [LoggerController::class, 'logTo'])->name('to');
        Route::post('/log-to-all', [LoggerController::class, 'logToAll'])->name('to.all');
    });

    Route::prefix('subscriptions/price')->as('subscriptions.price.')->group(function () {
        Route::post('/', [PriceSubscriptionController::class, 'store'])->name('store');
        Route::get('/', [PriceSubscriptionController::class, 'index'])->name('index');
        Route::delete('/{priceSubscription}', [PriceSubscriptionController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('subscriptions/price')->as('subscriptions.price.')->group(function () {
    Route::get('/confirm', [PriceSubscriptionController::class, 'confirm'])->name('confirm');
});
