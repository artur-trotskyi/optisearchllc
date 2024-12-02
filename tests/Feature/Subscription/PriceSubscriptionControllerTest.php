<?php

namespace Tests\Feature\Subscription;

use App\Enums\Subscription\PriceSubscriptionEnum;
use App\Models\PriceSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceSubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_get_price_subscriptions_with_user_scope(): void
    {
        PriceSubscription::factory()->count(20)->create(['user_id' => $this->user->id]);
        PriceSubscription::factory()->count(5)->create(['user_id' => User::factory()->create()->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('subscriptions.price.index', ['per_page' => 25, 'page' => 1]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'success',
                'message',
            ]);

        $data = $response->json('data')['data'];
        foreach ($data as $item) {
            $this->assertEquals($this->user->id, $item['userId']);
        }

        $this->assertCount(20, $data);
    }

    public function test_cannot_access_without_authentication(): void
    {
        $response = $this->getJson(route('subscriptions.price.index'));

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ])->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_can_create_price_subscription(): void
    {
        $subscriptionData = [
            'user_id' => $this->user->id,
            'url' => 'https://example.com',
            'email' => 'user@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('subscriptions.price.store'), $subscriptionData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => PriceSubscriptionEnum::CREATED->message(),
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'userId',
                    'url',
                    'email',
                    'price',
                    'createdAt',
                    'updatedAt',
                ],
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('price_subscriptions', $subscriptionData);
    }

    public function test_cannot_create_price_subscription_if_not_authenticated(): void
    {
        $subscriptionData = [
            'user_id' => $this->user->id,
            'url' => 'https://example.com',
            'email' => 'user@example.com',
        ];

        $response = $this->postJson(route('subscriptions.price.store'), $subscriptionData);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_can_delete_own_price_subscription(): void
    {
        $subscription = PriceSubscription::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson(route('subscriptions.price.destroy', $subscription->id));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Price subscription successfully deleted.',
                'success' => true,
                'data' => [],
            ]);

        $this->assertSoftDeleted('price_subscriptions', ['id' => $subscription->id]);
    }

    public function test_cannot_delete_other_users_price_subscription(): void
    {
        $anotherUser = User::factory()->create();
        $subscription = PriceSubscription::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson(route('subscriptions.price.destroy', $subscription->id));

        $response->assertStatus(403)
            ->assertJsonFragment([
                'message' => 'Unauthorized action.',
                'success' => false,
                'data' => [
                    'errors' => [
                        'The current user does not have ownership rights to the requested resource.',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('price_subscriptions', ['id' => $subscription->id]);
    }

    public function test_cannot_delete_price_subscription_if_not_authenticated(): void
    {
        $subscription = PriceSubscription::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson(route('subscriptions.price.destroy', $subscription->id));

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
                'success' => false,
                'data' => [
                    'errors' => [
                        'Authentication is required to access this resource.',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('price_subscriptions', ['id' => $subscription->id]);
    }
}
