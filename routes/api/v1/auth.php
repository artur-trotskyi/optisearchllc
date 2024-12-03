<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SanctumAuthController;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

// Resolve the appropriate authentication controller instance
/** @var AuthService $authService */
$authService = app(AuthService::class);
$authController = $authService->resolveAuthController();

Route::prefix('auth')->as('auth.')->group(function () use ($authController): void {
    Route::post('register', [$authController, 'register'])->name('register');
    Route::post('login', [$authController, 'login'])->name('login');

    Route::middleware(['verified'])->group(function () use ($authController): void {
        Route::post('logout', [$authController, 'logout'])->name('logout');
        Route::post('refresh-token', [$authController, 'refresh'])->name('refresh');
        Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
        Route::post('me', [$authController, 'me'])->name('me');
    });
});

Route::prefix('auth')->as('auth.')->group(function (): void {
    Route::post('/register-with-verify', [SanctumAuthController::class, 'registerWithEmailVerification'])->name('register-with-verify');
    Route::get('/email/verify', [EmailVerificationController::class, 'verify'])->name('verify.email');
});
