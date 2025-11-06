<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Patron;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'patron_id' => Patron::factory(),
            'loaned_at' => $this->faker->date(),
            'due_at' => $this->faker->date('+2 weeks'),
        ];
    }
}
