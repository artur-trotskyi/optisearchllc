<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\PriceSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class PriceSubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PriceSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => fake()->url(),
            'email' => fake()->unique()->safeEmail(),
            'price' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
