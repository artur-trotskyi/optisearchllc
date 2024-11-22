<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default users
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@email.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            Order::factory()->count(250)->create([
                'user_id' => $user->id,
            ]);

        }

        // Create additional users and orders
        User::factory()
            ->count(10)
            ->has(Order::factory()->count(20))
            ->create();
    }
}
