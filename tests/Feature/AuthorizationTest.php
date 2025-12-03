<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
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

    public function test_patron_only_sees_their_loans()
    {
        $patronUser = $this->createPatronUser();
        $otherUser = $this->createPatronUser();

        $patronLoan = Loan::factory()
            ->for(Book::factory())
            ->for($patronUser->patron)
            ->create();

        $otherLoan = Loan::factory()
            ->for(Book::factory())
            ->for($otherUser->patron)
            ->create();

        $this->actingAs($patronUser, 'api')
            ->getJson('/api/v1/loans')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $patronLoan->id,
            ])
            ->assertJsonMissing([
                'id' => $otherLoan->id,
            ]);
    }

    public function test_patron_cannot_view_other_patrons_loan()
    {
        $patronUser = $this->createPatronUser();
        $patronLoan = Loan::factory()
            ->for(Book::factory())
            ->for($patronUser->patron)
            ->create();

        $otherLoan = Loan::factory()
            ->for(Book::factory())
            ->for($this->createPatronUser()->patron)
            ->create();

        $this->actingAs($patronUser, 'api')
            ->getJson("/api/v1/loans/{$patronLoan->id}")
            ->assertOk()
            ->assertJsonFragment([
                'id' => $patronLoan->id,
            ]);

        $this->actingAs($patronUser, 'api')
            ->getJson("/api/v1/loans/{$otherLoan->id}")
            ->assertForbidden();
    }

    public function test_patron_can_view_their_profile()
    {
        $patronUser = $this->createPatronUser();

        $this->actingAs($patronUser, 'api')
            ->getJson('/api/v1/patrons/me')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $patronUser->patron->id,
                'email' => $patronUser->email,
            ]);
    }
}
