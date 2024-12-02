<?php

namespace App\Services\Subscription\PriceSubscription;

use App\Models\PriceSubscription;
use Illuminate\Support\Facades\Log;

class PriceSubscriptionServiceFactory
{
    public function make(PriceSubscription $subscription): ?PriceSubscriptionServiceInterface
    {
        $url = $subscription->getAttribute('url');

        if (str_contains($url, 'olx.ua')) {
            return app(OlxPriceSubscriptionService::class);
        }

        Log::warning("No service found for URL: $url");

        return null;
    }
}
