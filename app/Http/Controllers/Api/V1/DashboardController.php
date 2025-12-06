<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $booksCount = Book::count();
        $loansCount = Loan::count();
        $activeLoansCount = Loan::whereNull('returned_at')->count();
        $totalPatrons = Patron::count();
        $activePatronsCount = $totalPatrons; // TODO: filter by active status once field exists.
        $recentBooks = Book::orderByDesc('created_at')
            ->take(5)
            ->get(['id', 'title', 'author', 'genre', 'created_at']);

        return response()->json([
            'data' => [
                'total_books' => $booksCount,
                'available_books' => Book::where('status', 'available')->count(),
                'loaned_books' => Book::where('status', 'loaned')->count(),
                'total_patrons' => $totalPatrons,
                'loans_count' => $loansCount,
                'active_loans_count' => $activeLoansCount,
                'active_patrons_count' => $activePatronsCount,
                'recent_books' => $recentBooks,
            ],
        ]);
    }
}
