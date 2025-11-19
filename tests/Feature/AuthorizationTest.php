<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    protected function createPatronUser(): User
    {
        return User::factory()->create([
            'role' => 'patron',
        ]);
    }

    public function test_guest_cannot_create_book()
    {
        $payload = [
            'title' => 'Unauthorized Book',
            'author' => 'Unknown Author',
        ];

        $this->postJson('/api/v1/books', $payload)
            ->assertUnauthorized(); // 401
    }

    public function test_patron_cannot_create_book()
    {
        $patron = $this->createPatronUser();

        $payload = [
            'title' => 'Unauthorized Book',
            'author' => 'Unknown Author',
        ];

        $this->actingAs($patron, 'api')
            ->postJson('/api/v1/books', $payload)
            ->assertForbidden(); // 403
    }

    public function test_admin_can_create_book()
    {
        $admin = $this->createAdminUser();

        $payload = [
            'title' => 'Authorized Book',
            'author' => 'Admin Author',
        ];

        $this->actingAs($admin, 'api')
            ->postJson('/api/v1/books', $payload)
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'Authorized Book',
                'author' => 'Admin Author',
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Authorized Book',
        ]);
    }
}
