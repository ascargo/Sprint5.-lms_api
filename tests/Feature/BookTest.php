<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Laravel\Passport\Passport;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function actingAsAdmin(): User
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Passport::actingAs($user);

        return $user;
    }

    public function test_books_index_returns_empty_list()
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ]);
    }

    public function test_it_creates_a_book()
    {
        $this->actingAsAdmin();

        $payload = [
            'title' => 'Vivir aquí y ahora',
            'author' => 'Sergio Forgas Berdet',
            'isbn' => '978-84-949984-6-1',
            'year' => 2021,
            'genre' => 'Gestalt',
            'collection' => 'therapy',
            'location' => 'home',
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'Vivir aquí y ahora',
                'author' => 'Sergio Forgas Berdet',
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Vivir aquí y ahora',
            'isbn' => '978-84-949984-6-1',
        ]);
    }

    public function test_it_shows_a_single_book()
    {
        $this->actingAsAdmin();

        $book = \App\Models\Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'isbn' => '9780451524935',
            'year' => 1949,
            'genre' => 'Current History',
            'collection' => 'Classics',
            'location' => 'home',
            'cover_path' => null,
        ]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
        ->assertJsonFragment([
            'title' => '1984',
            'author' => 'George Orwell',
        ]);
    }

    public function test_it_updates_a_book(): void
    {
        $this->actingAsAdmin();

        $book = Book::create([
            'title' => 'Old Title',
            'author' => 'Unknown Author',
        ]);

        $updatedData = [
            'title' => 'New Title',
            'author' => 'Famous Author',
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $updatedData);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'New Title',
                'author' => 'Famous Author',
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'New Title',
            'author' => 'Famous Author',
        ]);
    }

    public function test_it_deletes_a_book(): void
    {
        $this->actingAsAdmin();

        $book = Book::create([
            'title' => 'To Delete',
            'author' => 'Author Name',
        ]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

}
