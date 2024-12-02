<?php

namespace App\Providers;

use App\Enums\Auth\AuthDriverEnum;
use App\Enums\Auth\TokenAbilityEnum;
use App\Models\Order;
use App\Models\PriceSubscription;
use App\Policies\OrderPolicy;
use App\Policies\PriceSubscriptionPolicy;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        if (config('auth.auth_driver') === AuthDriverEnum::SANCTUM->message()) {
            Auth::shouldUse('sanctum');
            $this->overrideSanctumConfigurationToSupportRefreshToken();
        }

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(PriceSubscription::class, PriceSubscriptionPolicy::class);

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
    }

    /**
     * Configure the rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perSecond(5)->by($request->user()->id)
                : Limit::perSecond(5)->by($request->ip());
        });
    }

    /**
     * Override Sanctum's default configuration to support handling both access tokens and refresh tokens.
     */
    private function overrideSanctumConfigurationToSupportRefreshToken(): void
    {
        Sanctum::$accessTokenAuthenticationCallback = function ($accessToken, $isValid) {
            $abilities = collect($accessToken->abilities);
            if (! empty($abilities) && $abilities[0] === TokenAbilityEnum::ISSUE_ACCESS_TOKEN->message()) {
                return $accessToken->expires_at && $accessToken->expires_at->isFuture();
            }

            return $isValid;
        };

        Sanctum::$accessTokenRetrievalCallback = function ($request) {
            if (! $request->routeIs('auth.refresh')) {
                return str_replace('Bearer ', '', $request->headers->get('Authorization'));
            }

            return $request->cookie('refreshToken') ?? '';
        };
    }
}
