<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['available', 'loaned', 'lost', 'reserved'];

        Book::factory()
            ->count(40)
            ->state(function () use ($statuses) {
                return [
                    'status' => $statuses[array_rand($statuses)],
                ];
            })
            ->create();
    }
}
