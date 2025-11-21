<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Book;

class BookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        );
    }

    public function test_books_index_returns_empty_list()
    {
        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'total'
            ]);
    }

    public function test_it_creates_a_book()
    {
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
            'isbn' => '978-84-949984-6-1',
        ]);
    }

    public function test_it_shows_a_single_book()
    {
        $book = Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'isbn' => '9780451524935',
            'year' => 1949,
            'genre' => 'Current History',
            'collection' => 'Classics',
            'location' => 'home',
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
        ]);
    }

    public function test_it_deletes_a_book(): void
    {
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

    public function test_it_filters_books_by_status()
    {
        Book::factory()->create(['status' => 'available']);
        Book::factory()->create(['status' => 'loaned']);

        $response = $this->getJson('/api/v1/books?status=available');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('available', $response->json('data')[0]['status']);
    }

    public function test_it_filters_books_by_author()
    {
        Book::factory()->create(['author' => 'Sergio Forgas']);
        Book::factory()->create(['author' => 'Someone Else']);

        $response = $this->getJson('/api/v1/books?author=Sergio');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_books_are_paginated()
    {
        Book::factory()->count(30)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]);

        $this->assertCount(10, $response->json('data'));
    }

}
