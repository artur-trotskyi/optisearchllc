<?php

namespace Database\Factories;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(OrderStatusEnum::cases());
        $deletedAt = $status === OrderStatusEnum::DELETED ? Carbon::now() : null;

        return [
            'user_id' => User::factory(),
            'product_name' => fake()->word(),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'status' => $status,
            'deleted_at' => $deletedAt,
        ];
    }
}
