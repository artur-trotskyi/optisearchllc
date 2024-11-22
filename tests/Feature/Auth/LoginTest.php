<?php

namespace Tests\Feature\Auth;

use App\Enums\Exception\ExceptionMessagesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_login_without_required_fields(): void
    {
        $response = $this->postJson(route('auth.login'));
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'success' => false,
            'message' => ExceptionMessagesEnum::TheGivenDataWasInvalid->message(),
            'data' => [
                'errors' => [
                    'The email field is required. (and 1 more error)',
                ],
            ],
        ]);
    }

    public function test_cannot_login_with_wrong_password(): void
    {
        $user = AuthTestHelper::mockUser();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => ExceptionMessagesEnum::TheGivenDataWasInvalid->message(),
            'data' => [
                'errors' => [
                    ExceptionMessagesEnum::TheProvidedCredentialsAreIncorrect->message(),
                ],
            ],
        ]);

        AuthTestHelper::clearUser($user);
    }

    public function test_cannot_login_with_wrong_email(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'unexists@mail.example',
            'password' => 'incorrect',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => ExceptionMessagesEnum::TheGivenDataWasInvalid->message(),
            'data' => [
                'errors' => [
                    'The selected email is invalid.',
                ],
            ],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function test_can_login(): void
    {
        $user = AuthTestHelper::mockUser();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertCookieNotExpired(
            'refreshToken'
        );

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => AuthTestHelper::$loginSuccessBody,
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }

    /**
     * @throws Throwable
     */
    public function test_can_refresh_token(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $moveTime = config('sanctum.expiration') + 5;
        $this->travel($moveTime)->minutes();
        $this->assertFalse(AuthTestHelper::verifyAccessToken($tokens['accessToken']));

        $response = $this
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withCredentials()
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accessToken',
            ],
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }

    /**
     * @throws Throwable
     */
    public function test_can_refresh_token_after_login(): void
    {
        $user = AuthTestHelper::mockUser();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertCookieNotExpired('refreshToken');
        $refreshToken = $response->getCookie('refreshToken', false)->getValue();
        $accessToken = $response->decodeResponseJson()['data']['accessToken'];

        // Manually make access token expired
        $moveTime = config('sanctum.expiration') + 5;
        $this->travel($moveTime)->minutes();
        $this->assertFalse(AuthTestHelper::verifyAccessToken($accessToken));

        $response = $this
            ->withUnencryptedCookie('refreshToken', $refreshToken)
            ->withCredentials()
            ->withHeader('Authorization', 'Bearer '.$refreshToken)
            ->postJson(route('auth.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accessToken',
            ],
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }

    public function test_access_token_expiration(): void
    {
        $user = AuthTestHelper::mockUser(true);
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $this->travel(config('sanctum.expiration') + 10)->minutes();

        $response = $this->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.me'));

        $response->assertStatus(401);
        $response->assertJson([
            'message' => ExceptionMessagesEnum::AuthenticationRequired->message(),
        ]);

        AuthTestHelper::clearUser($user);
    }

    public function test_refresh_token_expiration(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $this->travel(config('sanctum.rt_expiration') + 5)->minutes();

        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.refresh'));

        $response->assertStatus(401);
        $response->assertJson([
            'message' => ExceptionMessagesEnum::AuthenticationRequired->message(),
        ]);

        AuthTestHelper::clearUser($user);
    }
}
