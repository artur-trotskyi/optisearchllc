<?php

use App\Http\Controllers\Api\V1\LoggerController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('orders', OrderController::class);

Route::prefix('loggers')->as('loggers.')->group(function () {
    Route::post('/log', [LoggerController::class, 'log'])->name('default');
    Route::post('/log-to/{type}', [LoggerController::class, 'logTo'])->name('to');
    Route::post('/log-to-all', [LoggerController::class, 'logToAll'])->name('to.all');
});
