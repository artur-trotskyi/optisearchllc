<?php

namespace App\Services\Subscription\PriceSubscription;

interface PriceSubscriptionServiceInterface
{
    public function fetchPrice(string $url): ?float;
}
