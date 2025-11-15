<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Patron;
use Laravel\Passport\Passport;
use App\Models\user;

class PatronTest extends TestCase
{
    use RefreshDatabase;

    public function test_patrons_index_returns_empty_list()
    {
        Passport::actingAs(User::factory()->create());

        $response = $this->getJson('/api/v1/patrons');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ]);
    }

    public function test_it_creates_a_patron()
    {
        Passport::actingAs(User::factory()->create());

        $payload = [
            'name' => 'Asier Comino',
            'email' => 'asier@example.com',
        ];

        $response = $this->postJson('/api/v1/patrons', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Asier Comino',
                'email' => 'asier@example.com',
            ]);

        $this->assertDatabaseHas('patrons', [
            'email' => 'asier@example.com',
        ]);
    }

    public function test_it_shows_a_single_patron()
    {
        Passport::actingAs(User::factory()->create());

        $patron = Patron::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response = $this->getJson("/api/v1/patrons/{$patron->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ]);
    }

    public function test_it_updates_a_patron()
    {
        Passport::actingAs(User::factory()->create());

        $patron = Patron::create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $updated = [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ];

        $response = $this->putJson("/api/v1/patrons/{$patron->id}", $updated);

        $response->assertOk()
            ->assertJsonFragment($updated);

        $this->assertDatabaseHas('patrons', $updated);
    }

    public function test_it_deletes_a_patron()
    {
        Passport::actingAs(User::factory()->create());

        $patron = Patron::create([
            'name' => 'To Delete',
            'email' => 'delete@example.com',
        ]);

        $response = $this->deleteJson("/api/v1/patrons/{$patron->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('patrons', [
            'id' => $patron->id,
        ]);
    }
}
