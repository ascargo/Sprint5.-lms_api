<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Patron;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::where('status', 'available')->get();
        $patrons = Patron::all();

        Loan::factory()->count(5)->make()->each(function ($loan) use ($books, $patrons) {

            $loan->book_id   = $books->random()->id;
            $loan->patron_id = $patrons->random()->id;
            $loan->save();

            Book::find($loan->book_id)->update(['status' => 'loaned']);
        });
    }
}
