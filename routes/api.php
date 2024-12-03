<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api', 'api'])->prefix('v1')->group(function () {
    Route::group([], function () {
        require base_path('routes/api/v1/auth.php');
    });

    Route::middleware([])->group(function () {
        require base_path('routes/api/v1/api.php');
    })->middleware(['verified']);
});
