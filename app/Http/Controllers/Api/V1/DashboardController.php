<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => [
                'total_books'     => Book::count(),
                'available_books' => Book::where('status', 'available')->count(),
                'loaned_books'    => Book::where('status', 'loaned')->count(),
                'total_patrons'   => Patron::count(),
                'active_loans'    => Loan::count(),
            ]
        ]);
    }
}
