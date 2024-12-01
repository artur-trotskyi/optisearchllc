<?php

namespace App\Services\Subscription\PriceSubscription;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class OlxPriceSubscriptionService extends BasePriceSubscriptionService implements PriceSubscriptionServiceInterface
{
    public function fetchPrice(string $url): ?float
    {
        $response = Http::get($url);
        if (! $response->successful()) {
            return null;
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        try {
            $scriptContent = $crawler->filter('script[type="application/ld+json"]')->first()->text();
            $jsonData = json_decode($scriptContent, true);

            return isset($jsonData['offers']['price']) ? (float) $jsonData['offers']['price'] : null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
