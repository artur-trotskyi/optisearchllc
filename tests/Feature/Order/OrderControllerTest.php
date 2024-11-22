<?php

namespace Tests\Feature\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\ResourceMessagesEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Event::fake();

        $this->user = User::factory()->create();
    }

    public function test_can_retrieve_orders(): void
    {
        Order::factory()->count(10)->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('orders.index', ['page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'success',
                'message',
            ])
            ->assertJsonFragment([
                'success' => true,
                'message' => ResourceMessagesEnum::DataRetrievedSuccessfully->message(),
            ]);
    }

    public function test_can_filter_orders_by_status(): void
    {
        Order::factory()->count(1)->create(['user_id' => $this->user->id, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);
        Order::factory()->count(1)->create(['user_id' => $this->user->id, 'status' => OrderStatusEnum::SENT, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('orders.index', ['status' => OrderStatusEnum::PROCESSING, 'page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.orders');
    }

    public function test_can_sort_orders(): void
    {
        Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);
        Order::factory()->create(['user_id' => $this->user->id, 'amount' => 50, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('orders.index', ['sortBy' => 'amount', 'orderBy' => 'desc', 'page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonPath('data.orders.0.amount', '100.00');
    }

    public function test_cannot_access_orders_without_authentication(): void
    {
        $response = $this->getJson(route('orders.index', ['page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_can_create_order(): void
    {
        $orderData = [
            'user_id' => $this->user->id,
            'product_name' => 'Test Product',
            'amount' => 100,
            'status' => OrderStatusEnum::PROCESSING,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('orders.store'), $orderData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Data created successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'userId',
                    'productName',
                    'amount',
                    'status',
                    'createdAt',
                    'updatedAt',
                ],
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('orders', $orderData);
    }

    public function test_cannot_create_order_if_not_authenticated(): void
    {
        $orderData = [
            'user_id' => $this->user->id,
            'product_name' => 'Test Product',
            'amount' => 100,
            'status' => OrderStatusEnum::PROCESSING,
        ];

        $response = $this->postJson(route('orders.store'), $orderData);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_can_view_own_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('orders.show', $order->id));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Data retrieved successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'userId',
                    'productName',
                    'amount',
                    'status',
                    'createdAt',
                    'updatedAt',
                ],
                'success',
                'message',
            ]);
    }

    public function test_cannot_view_other_users_order(): void
    {
        $anotherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $anotherUser->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('orders.show', $order->id));

        $response->assertStatus(403)
            ->assertJsonFragment([
                'data' => [
                    'errors' => [
                        'The current user does not have ownership rights to the requested resource.',
                    ],
                ],
                'message' => 'Unauthorized action.',
                'success' => false,
            ]);
    }

    public function test_cannot_view_order_if_not_authenticated(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->getJson(route('orders.show', $order->id));

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
                'success' => false,
            ]);
    }

    public function test_can_update_own_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $updatedData = [
            'product_name' => 'Updated Product',
            'amount' => 150,
            'status' => OrderStatusEnum::PROCESSING,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson(route('orders.update', $order->id), $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Data updated successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('orders', array_merge(['id' => $order->id], $updatedData));
    }

    public function test_cannot_update_other_users_order(): void
    {
        $anotherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $anotherUser->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $updatedData = [
            'product_name' => 'Updated Product',
            'amount' => 150,
            'status' => OrderStatusEnum::SENT,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson(route('orders.update', $order->id), $updatedData);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'message' => 'Unauthorized action.',
                'success' => false,
            ]);
    }

    public function test_cannot_update_order_if_not_authenticated(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $updatedData = [
            'product_name' => 'Updated Product',
            'amount' => 150,
            'status' => OrderStatusEnum::SENT,
        ];

        $response = $this->putJson(route('orders.update', $order->id), $updatedData);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
                'success' => false,
            ]);
    }

    public function test_can_delete_own_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson(route('orders.destroy', $order->id));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Data deleted successfully.',
                'success' => true,

            ]);

        $order = Order::withTrashed()->find($order->id);
        $this->assertNotNull($order->deleted_at);
    }

    public function test_cannot_delete_other_users_order(): void
    {
        $anotherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $anotherUser->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson(route('orders.destroy', $order->id));

        $response->assertStatus(403)
            ->assertJsonFragment([
                'message' => 'Unauthorized action.',
                'success' => false,
            ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_cannot_delete_order_if_not_authenticated(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'status' => OrderStatusEnum::PROCESSING, 'deleted_at' => null]);

        $response = $this->deleteJson(route('orders.destroy', $order->id));

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
                'success' => false,
            ]);
    }
}
