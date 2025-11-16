<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Patron;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function actingAsAdmin(): User
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        Passport::actingAs($user);
        return $user;
    }

    public function test_loans_index_returns_empty_list()
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/loans');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_it_creates_a_loan()
    {
        $this->actingAsAdmin();

        $book = Book::factory()->create();
        $patron = Patron::factory()->create();

        $payload = [
            'book_id' => $book->id,
            'patron_id' => $patron->id,
            'loaned_at' => '2024-01-01',
            'due_at' => '2024-01-10',
        ];

        $response = $this->postJson('/api/v1/loans', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'book_id' => $book->id,
                'patron_id' => $patron->id,
            ]);

        $this->assertDatabaseHas('loans', [
            'book_id' => $book->id,
            'patron_id' => $patron->id,
        ]);
    }

    public function test_it_lists_loans(): void
    {
        $this->actingAsAdmin();

        $loan = Loan::factory()
            ->for(Book::factory())
            ->for(Patron::factory())
            ->create();

        $response = $this->getJson('/api/v1/loans');

        $response->assertOk()
            ->assertJsonFragment([
                'book_id' => $loan->book_id,
                'patron_id' => $loan->patron_id,
            ]);
    }

    public function test_it_shows_a_loan(): void
    {
        $this->actingAsAdmin();

        $loan = Loan::factory()
            ->for(Book::factory())
            ->for(Patron::factory())
            ->create();

        $response = $this->getJson("/api/v1/loans/{$loan->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $loan->id,
                'book_id' => $loan->book_id,
                'patron_id' => $loan->patron_id,
            ]);
    }

    public function test_it_updates_a_loan(): void
    {
        $this->actingAsAdmin();

        $loan = Loan::factory()
            ->for(Book::factory())
            ->for(Patron::factory())
            ->create([
                'loaned_at' => '2024-01-01',
                'due_at' => '2024-01-10',
            ]);

        $newDueDate = now()->addDays(14)->toDateString();

        $response = $this->putJson("/api/v1/loans/{$loan->id}", [
            'due_at' => $newDueDate,
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $loan->id,
                'due_at' => $newDueDate,
            ]);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'due_at' => $newDueDate,
        ]);
    }

    public function test_it_deletes_a_loan(): void
    {
        $this->actingAsAdmin();

        $loan = Loan::factory()
            ->for(Book::factory())
            ->for(Patron::factory())
            ->create();

        $response = $this->deleteJson("/api/v1/loans/{$loan->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('loans', [
            'id' => $loan->id,
        ]);
    }
}
