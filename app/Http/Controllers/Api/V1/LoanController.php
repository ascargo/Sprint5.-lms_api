<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $perPageParam = $request->query('per_page', 25);
        $page = max(1, (int) $request->query('page', 1));

        $query = Loan::query();

        if ($user->role === 'patron') {
            $query->where('patron_id', $user->id);
        }

        if ($perPageParam === 'all') {
            return response()->json([
                'data' => $query->get(),
            ]);
        }

        $perPage = max(1, min((int) $perPageParam, 1000));

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
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

    public function myLoans(Request $request): JsonResponse
    {
        $user = auth()->user();

        $perPageParam = $request->query('per_page', 25);
        $page = max(1, (int) $request->query('page', 1));

        $query = Loan::with(['book', 'patron'])
            ->where('patron_id', $user->id);

        if ($perPageParam === 'all') {
            return response()->json([
                'data' => $query->get(),
            ]);
        }

        $perPage = max(1, min((int) $perPageParam, 1000));

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    public function show(Loan $loan): JsonResponse
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $loan->patron_id !== $user->id) {
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
        ]);

        $loan->update($validated);

        return response()->json([
            'data' => $loan->load(['book', 'patron']),
            'message' => 'Loan updated successfully',
        ]);
    }

    public function requestExtension(Request $request, Loan $loan): JsonResponse
    {
        $user = auth()->user();

        if ($loan->patron_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'due_at' => 'required|date|after:' . $loan->due_at,
            // TODO: if a dedicated requested_return_date column is added, switch to it here.
        ]);

        $loan->update(['due_at' => $validated['due_at']]);

        return response()->json([
            'data' => $loan->load(['book', 'patron']),
            'message' => 'Extension requested successfully',
        ]);
    }

    public function destroy(Loan $loan): JsonResponse
    {
        $book = $loan->book;
        $loan->delete();
        $book->update(['status' => 'available']);

        return response()->json(null, 204);
    }
}
