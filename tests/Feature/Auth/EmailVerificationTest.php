<?php

namespace Tests\Feature\Auth;

use App\Enums\ResourceMessagesEnum;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_user_not_found(): void
    {
        $response = $this->getJson(route('auth.verify.email', [
            'id' => 999,
            'hash' => sha1('nonexistent@example.com'),
        ]));

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => ResourceMessagesEnum::UserNotFound->message(),
                'data' => [],
            ]);
    }

    public function test_verify_invalid_hash(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('auth.verify.email', [
            'id' => $user->id,
            'hash' => 'invalidhash',
        ]));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => ResourceMessagesEnum::InvalidHash->message(),
                'data' => [],
            ]);
    }

    public function test_verify_email_already_verified(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->getJson(route('auth.verify.email', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => ResourceMessagesEnum::EmailAlreadyVerified->message(),
                'data' => [],
            ]);
    }

    public function test_verify_email_successfully(): void
    {
        Event::fake();

        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->getJson(route('auth.verify.email', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => ResourceMessagesEnum::EmailVerifiedSuccessfully->message(),
                'data' => [],
            ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class, function ($event) use ($user) {
            return $event->user->is($user);
        });
    }
}
