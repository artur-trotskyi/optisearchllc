<?php

namespace Tests\Feature\Logger;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoggerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_log_with_valid_data(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('loggers.default'), $loggerData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_log_unauthenticated(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->postJson(route('loggers.default'), $loggerData);

        $response->assertStatus(401);
    }

    public function test_log_missing_parameter(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('loggers.default'));

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                'The message field is required.',
            ],
        ]);
    }

    public function test_log_to_specific_logger(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(
            route('loggers.to', ['type' => 'file']),
            $loggerData
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_log_to_specific_logger_unauthenticated(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->postJson(route('loggers.to', ['type' => 'database']), $loggerData);

        $response->assertStatus(401);
    }

    public function test_log_to_specific_logger_missing_parameter(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson(
            route('loggers.to', ['type' => 'email'])
        );

        $response->assertJsonFragment([
            'errors' => [
                'The message field is required.',
            ],
        ]);
    }

    public function test_log_to_unknown_logger_type(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(
            route('loggers.to', ['type' => 'unknown']),
            $loggerData
        );

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error.',
                'data' => [
                    'errors' => [
                        "Logger type 'unknown' is not supported.",
                    ],
                ],

            ]);
    }

    public function test_log_to_all_loggers(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('loggers.to.all'), $loggerData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_log_to_all_loggers_unauthorized(): void
    {
        $loggerData = [
            'message' => 'Test logger message',
        ];

        $response = $this->postJson(route('loggers.to.all'), $loggerData);

        $response->assertStatus(401);
    }

    public function test_log_to_all_loggers_missing_parameter(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('loggers.to.all'));

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                'The message field is required.',
            ],
        ]);
    }
}
