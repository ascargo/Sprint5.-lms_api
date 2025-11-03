<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_index_returns_empty_list()
    {
        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
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
            'title' => 'Vivir aquí y ahora',
            'isbn' => '978-84-949984-6-1',
        ]);
    }
}
