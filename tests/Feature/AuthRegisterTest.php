<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token','token_type']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_validation_fails_for_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name','email','password']);
    }
}
