<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;
use App\Models\Patron;
use Carbon\Carbon;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        $loanedAt = Carbon::now()->subDays(rand(1, 30));
        $dueAt = (clone $loanedAt)->addDays(rand(7, 21));

        return [
            'book_id' => Book::factory(),
            'patron_id' => Patron::factory(),
            'loaned_at' => $loanedAt->toDateString(),
            'due_at' => $dueAt->toDateString(),
        ];
    }
}
