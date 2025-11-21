<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_overall_dashboard_stats()
    {
        Book::factory()->count(5)->create(['status' => 'available']);
        Book::factory()->count(2)->create(['status' => 'loaned']);

        Patron::factory()->count(3)->create();

        $loanedBook = Book::factory()->create(['status' => 'loaned']);
        Loan::factory()->create([
            'book_id' => $loanedBook->id,
            'patron_id' => Patron::factory()->create()->id,
            'loaned_at' => now()->subDays(5)->toDateString(),
            'due_at' => now()->addDays(5)->toDateString(),
        ]);

        $overdueBook = Book::factory()->create(['status' => 'loaned']);
        Loan::factory()->create([
            'book_id'   => $overdueBook->id,
            'patron_id' => Patron::factory()->create()->id,
            'loaned_at' => now()->subDays(10)->toDateString(),
            'due_at'    => now()->subDays(1)->toDateString(), // past due
            'returned_at' => null,
        ]);

        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');

        $response = $this->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'total_books',
                    'available_books',
                    'loaned_books',
                    'total_patrons',
                    'active_loans',
                    'overdue_loans',
                ]
            ])
            ->assertJsonFragment([
                'total_books' => 9,
                'available_books' => 5,
                'loaned_books' => 4,
                'total_patrons' => 5,
                'active_loans' => 2,
                'overdue_loans' => 1,
            ]);
    }
}
