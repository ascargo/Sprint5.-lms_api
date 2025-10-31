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
}
