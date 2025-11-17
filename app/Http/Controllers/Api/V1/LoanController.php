<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Patron;
use App\Http\Requests\LoanRequest;

class LoanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Loan::with(['book','patron'])->get(),
        ]);
    }

    public function store(LoanRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = Book::findOrFail($data['book_id']);

        if ($book->status !== 'available') {
            return response()->json([
                'message' => 'Book is not available for loan'
            ], 422);
        }

        $loan = Loan::create($data);

        $book->update(['status' => 'loaned']);

        return response()->json([
            'data' => $loan,
            'message' => 'Loan created successfully',
        ], 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        return response()->json([
            'data' => $loan->load(['book','patron']),
        ]);
    }

    public function update(LoanRequest $request, Loan $loan): JsonResponse
    {
        $data = $request->validated();

        $loan->update($data);

        return response()->json([
            'data' => $loan,
            'message' => 'Loan updated successfully'
        ], 200);
    }

    public function destroy(Loan $loan): JsonResponse
    {
        $loan->book->update(['status' => 'available']);

        $loan->delete();

        return response()->json(null, 204);
    }
}
