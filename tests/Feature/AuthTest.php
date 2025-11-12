<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_receive_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
                ->assertJsonStructure(['access_token','token_type']);
    }

    public function test_returns_the_authenticated_user_info(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonFragment([
                'email' => $user->email,
            ]);
    }

    public function test_guest_cannot_access_me_endpoint(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }
}
