<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPriceSubscriptionJob;
use App\Services\Subscription\PriceSubscription\BasePriceSubscriptionService;
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
            ProcessPriceSubscriptionJob::dispatch($url, $subscriptions);
        }

        $this->info('Price subscription monitoring completed.');
    }
}
