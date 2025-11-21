<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Patron;
use Laravel\Passport\Passport;
use App\Models\User;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(
            User::factory()->create([
                'role' => 'admin'
            ])
        );
    }


    public function test_loans_index_returns_empty_list()
    {
        $response = $this->getJson('/api/v1/loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'total'
            ]);
    }

    public function test_it_creates_a_loan()
    {
        $book = \App\Models\Book::factory()->create();
        $patron = \App\Models\Patron::factory()->create();

        $payload = [
            'book_id' => $book->id,
            'patron_id' => $patron->id,
            'loaned_at' => now()->toDateString(),
            'due_at' => now()->addDays(14)->toDateString(),
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
        $loan = Loan::factory()
        ->for(Book::factory())
        ->for(Patron::factory())
        ->create([
            'loaned_at' => now(),
            'due_at' => now()->addDays(7),
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
        $loan = Loan::factory()
            ->for(Book::factory())
            ->for(Patron::factory())
            ->create();

        $response = $this->deleteJson("/api/v1/loans/{$loan->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
    }

    public function test_cannot_create_loan_if_book_not_available()
    {
        $book = Book::factory()->create(['status' => 'loaned']);
        $patron = Patron::factory()->create();

        $payload = [
            'book_id' => $book->id,
            'patron_id' => $patron->id,
            'loaned_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->postJson('/api/v1/loans', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Book is not available for loan']);
    }

    public function test_book_status_changes_to_loaned_after_loan_created()
    {
        $book = Book::factory()->create(['status' => 'available']);
        $patron = Patron::factory()->create();

        $payload = [
            'book_id' => $book->id,
            'patron_id' => $patron->id,
            'loaned_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
        ];

        $this->postJson('/api/v1/loans', $payload)->assertCreated();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'loaned',
        ]);
    }

    public function test_book_becomes_available_after_loan_deleted()
    {
        $loan = Loan::factory()
            ->for(Book::factory()->state(['status' => 'available']))
            ->for(Patron::factory())
            ->create();

        // first loan => should set book to loaned
        $loan->book->update(['status' => 'loaned']);

        $this->deleteJson("/api/v1/loans/{$loan->id}")
            ->assertNoContent();

        $this->assertDatabaseHas('books', [
            'id' => $loan->book_id,
            'status' => 'available',
        ]);
    }
}
