<?php

use App\Enums\Auth\AuthDriverEnum;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api', 'api'])->prefix('v1')->group(function () {
    Route::group([], function () {
        require base_path('routes/api/v1/auth.php');
    });

    $authMiddleware = (config('auth.auth_driver') === AuthDriverEnum::SANCTUM->message()) ? 'auth:sanctum' : 'auth:api';
    Route::middleware([$authMiddleware])->group(function () {
        require base_path('routes/api/v1/api.php');
    });
});
