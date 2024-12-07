<?php

namespace App\Jobs;

use App\Services\Subscription\PriceSubscription\PriceSubscriptionServiceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPriceSubscriptionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $url,
        private readonly Collection $subscriptions
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PriceSubscriptionServiceFactory $serviceFactory): void
    {
        $service = $serviceFactory->make($this->subscriptions[0]);

        if (! $service) {
            return;
        }

        $newPrice = $service->fetchPrice($this->url);

        foreach ($this->subscriptions as $subscription) {
            if ($newPrice !== null && $newPrice !== $subscription->price) {
                $subscription->update(['price' => $newPrice]);
            }
        }
    }
}
