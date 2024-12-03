<?php

namespace Tests\Feature\Subscription;

use App\Models\PriceSubscription;
use App\Services\Subscription\PriceSubscription\PriceSubscriptionServiceFactory;
use App\Services\Subscription\PriceSubscription\PriceSubscriptionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MonitorPriceSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_updates_prices(): void
    {
        // Create test data
        $subscriptions = PriceSubscription::factory()
            ->count(2)
            ->sequence(
                ['url' => 'http://example.com', 'price' => 100, 'is_confirmed' => true],
                ['url' => 'http://example.com', 'price' => 200, 'is_confirmed' => true],
            )
            ->create();

        // Mock PriceSubscriptionService
        $mockPriceSubscriptionService = Mockery::mock(PriceSubscriptionServiceInterface::class);
        $mockPriceSubscriptionService
            ->shouldReceive('fetchPrice')
            ->with('http://example.com')
            ->andReturn(150);

        // Mock PriceSubscriptionServiceFactory
        $mockFactory = Mockery::mock(PriceSubscriptionServiceFactory::class);
        $mockFactory
            ->shouldReceive('make')
            ->andReturn($mockPriceSubscriptionService);

        // Replace the actual service in the container with the mock
        $this->app->instance(PriceSubscriptionServiceFactory::class, $mockFactory);

        // Execute the command
        $this->artisan('app:monitor-price-subscriptions')
            ->expectsOutput('Starting price subscription monitoring...')
            ->expectsOutput('Checking price for URL: http://example.com')
            ->expectsOutput('Price subscription monitoring completed.')
            ->assertExitCode(0);

        // Verify that the price was updated in the database
        $this->assertDatabaseHas('price_subscriptions', [
            'url' => 'http://example.com',
            'price' => 150,
        ]);
    }
}
