<?php

namespace Tests\Feature\Auth;

use App\Enums\ResourceMessagesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_successfully(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->actingAs($user)->postJson(route('auth.password.reset'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('success', true)
                ->where('message', ResourceMessagesEnum::PasswordResetSuccessful->message())
                ->etc()
            );

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_reset_password_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        // Авторизація користувача
        $response = $this->actingAs($user)->postJson(route('auth.password.reset'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
                'data' => [
                    'errors' => ['The provided current password is incorrect.'],
                ],
            ]);
    }

    public function test_reset_password_with_non_matching_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        // Авторизація користувача
        $response = $this->actingAs($user)->postJson(route('auth.password.reset'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
                'data' => [
                    'errors' => ['The password field confirmation does not match.'],
                ],
            ]);
    }

    public function test_reset_password_when_user_not_authenticated(): void
    {
        $response = $this->postJson(route('auth.password.reset'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Your email address is not verified.',
                'data' => [
                    'errors' => ['Your email address is not verified.'],
                ],
            ]);
    }
}
