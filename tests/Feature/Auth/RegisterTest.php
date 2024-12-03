<?php

namespace Tests\Feature\Auth;

use App\Enums\ResourceMessagesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected array $requestAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestAttributes = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }

    public function test_register_with_email_verification_successful(): void
    {
        Mail::fake();

        $data = $this->requestAttributes;
        $response = $this->postJson(route('auth.register-with-verify'), $data);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => ResourceMessagesEnum::VerificationEmailSent->message(),
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
        Mail::assertQueued(\App\Mail\VerifyEmailMail::class, function ($mail) use ($data) {
            return $mail->hasTo($data['email']);
        });
    }

    public function test_register_with_email_verification_fails_when_email_already_exists(): void
    {
        User::factory()->create([
            'email' => $this->requestAttributes['email'],
        ]);

        $response = $this->postJson(route('auth.register-with-verify'), $this->requestAttributes);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
                'data' => [
                    'errors' => ['The email has already been taken.'],
                ],
            ]);

        $this->assertDatabaseCount('users', 1);
    }
}
