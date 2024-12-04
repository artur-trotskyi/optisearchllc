<?php

namespace Feature\Product;

use App\Enums\ResourceMessagesEnum;
use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_can_retrieve_products(): void
    {
        Product::factory()->count(1000);
        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('products.index', ['page' => 1, 'itemsPerPage' => 20]));

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

    public function test_can_filter_products_by_title(): void
    {
        Currency::factory()->count(1)->create();
        Product::factory()->count(1)->create(['title' => 'Product Test Title']);
        Product::factory()->count(1)->create(['title' => 'Product Title']);

        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('products.index', ['q' => 'Test', 'page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products');
    }

    public function test_can_sort_products(): void
    {
        Currency::factory()->count(1)->create();
        Product::factory()->count(1)->create(['title' => 'Product Test Title', 'price' => 20.24]);
        Product::factory()->count(1)->create(['title' => 'Product Title', 'price' => 1000.01]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('products.index', ['sortBy' => 'price', 'orderBy' => 'asc', 'page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonPath('data.products.0.price', '20.24');

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('products.index', ['sortBy' => 'price', 'orderBy' => 'desc', 'page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(200)
            ->assertJsonPath('data.products.0.price', '1000.01');
    }

    public function test_cannot_access_products_without_authentication(): void
    {
        $response = $this->getJson(route('products.index', ['page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_cannot_access_products_if_not_verified(): void
    {
        $this->user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson(route('products.index', ['page' => 1, 'itemsPerPage' => 20]));

        $response->assertStatus(403)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Your email address is not verified.',
                'data' => [
                    'errors' => ['Your email address is not verified.'],
                ],
            ]);
    }
}
