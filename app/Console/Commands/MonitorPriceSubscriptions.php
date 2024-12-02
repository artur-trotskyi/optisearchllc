<?php

namespace App\Console\Commands;

use App\Services\Subscription\PriceSubscription\BasePriceSubscriptionService;
use App\Services\Subscription\PriceSubscription\PriceSubscriptionServiceFactory;
use Illuminate\Console\Command;

class MonitorPriceSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monitor-price-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor price subscriptions and notify users about price changes.';

    public function __construct(
        private readonly PriceSubscriptionServiceFactory $serviceFactory,
        private readonly BasePriceSubscriptionService $priceSubscriptionService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting price subscription monitoring...');

        $subscriptionsGroupedByUrl = $this->priceSubscriptionService->getAllConfirmed()->groupBy('url');

        foreach ($subscriptionsGroupedByUrl as $url => $subscriptions) {
            $this->info("Checking price for URL: {$url}");

            // Get the service
            $service = $this->serviceFactory->make($subscriptions->first());

            // If no service is returned, skip this URL and move on to the next
            if (! $service) {
                $this->warn("Skipping price update for URL: {$url} as no valid service was found.");

                continue;
            }

            // Fetch the new price
            $newPrice = $service->fetchPrice($url);

            foreach ($subscriptions as $subscription) {
                if ($newPrice !== null && $newPrice !== $subscription->price) {
                    $subscription->update(['price' => $newPrice]);
                    // See PriceSubscriptionObserver for further handling
                }
            }
        }

        $this->info('Price subscription monitoring completed.');
    }
}
