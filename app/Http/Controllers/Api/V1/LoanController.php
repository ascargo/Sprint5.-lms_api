<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $perPage = request()->integer('per_page', 10);

        if ($user->role !== 'admin') {
            $patronId = $user->patron?->id;

            $loans = Loan::with(['book', 'patron'])
                ->where('patron_id', $patronId ?? 0)
                ->paginate($perPage);
        } else {
            $loans = Loan::with(['book', 'patron'])->paginate($perPage);
        }

        return response()->json($loans);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'patron_id' => 'required|exists:patrons,id',
            'loaned_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:loaned_at',
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->status !== 'available') {
            return response()->json([
                'message' => 'Book is not available for loan',
            ], 422);
        }

        $loan = Loan::create($data);

        // update book status
        $book->update(['status' => 'loaned']);

        return response()->json([
            'data' => $loan->load(['book', 'patron']),
            'message' => 'Loan created successfully',
        ], 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $loan->patron_id !== $user->patron?->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['data' => $loan]);
    }

    public function update(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'sometimes|exists:books,id',
            'patron_id' => 'sometimes|exists:patrons,id',
            'loaned_at' => 'sometimes|date',
            'due_at' => 'sometimes|date|after_or_equal:loaned_at',
            'returned_at' => 'sometimes|nullable|date|after_or_equal:loaned_at',
        ]);

        $loan->update($validated);

        // If marking as returned, set book as available again.
        if (array_key_exists('returned_at', $validated) && $validated['returned_at']) {
            $loan->book?->update(['status' => 'available']);
        }

        return response()->json([
            'data' => $loan->load(['book', 'patron']),
            'message' => 'Loan updated successfully',
        ]);
    }

    public function destroy(Loan $loan): JsonResponse
    {
        $book = $loan->book;
        $loan->delete();
        $book->update(['status' => 'available']);

        return response()->json(null, 204);
    }

    /**
     * Dev/maintenance helper: align book.status with active loans.
     * - Any book with an active (not returned) loan is set to "loaned".
     * - Any book with no active loans is set to "available".
     * Only available in local environment; protect with auth:api + admin in routes.
     */
    public function syncBookStatuses(): JsonResponse
    {
        if (! app()->environment('local')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $updatedLoaned = 0;
        $updatedAvailable = 0;

        DB::transaction(function () use (&$updatedLoaned, &$updatedAvailable) {
            $activeBookIds = Loan::whereNull('returned_at')->pluck('book_id')->unique()->all();

            // Books with active loans should be marked as loaned
            if (! empty($activeBookIds)) {
                $updatedLoaned = Book::whereIn('id', $activeBookIds)
                    ->where('status', '!=', 'loaned')
                    ->update(['status' => 'loaned']);
            }

            // Books without active loans should be available
            $updatedAvailable = Book::whereNotIn('id', $activeBookIds)
                ->where('status', '!=', 'available')
                ->update(['status' => 'available']);
        });

        return response()->json([
            'message' => 'Book statuses synchronized',
            'updated_to_loaned' => $updatedLoaned,
            'updated_to_available' => $updatedAvailable,
        ]);
    }
}
