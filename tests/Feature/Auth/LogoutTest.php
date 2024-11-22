<?php

namespace Tests\Feature\Auth;

use App\Enums\ResourceMessagesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_logout(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withCredentials()
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.logout'));
        $response->assertStatus(200);

        $response->assertCookie('refreshToken', '', false);
        $this->assertEquals(0, PersonalAccessToken::where('tokenable_id', $user->id)->count());

        $response->assertJson([
            'success' => true,
            'message' => ResourceMessagesEnum::YouAreLoggedOut->message(),
            'data' => [],
        ]);

        AuthTestHelper::clearUser($user);
    }

    public function test_can_logout_with_expired_session(): void
    {
        $response = $this->postJson(route('auth.logout'));

        $response->assertStatus(200);

        $response->assertCookie('refreshToken');

        $response->assertJson([
            'success' => true,
            'message' => ResourceMessagesEnum::AlreadyLoggedOut->message(),
            'data' => [],
        ]);
    }
}
