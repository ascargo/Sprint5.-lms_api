<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Loan;

class LoanController extends Controller
{
    public function index(): JsonResponse
    {
        $loans = Loan::with(['book','patron'])->get();

        return response()->json([
            'data' => Loan::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'patron_id' => 'required|exists:patrons,id',
            'loaned_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:loaned_at',
        ]);

        $loan = Loan::create($data);

        return response()->json($loan, 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        return response()->json($loan);
    }

    public function update(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['exists:books,id'],
            'patron_id' => ['exists:patrons,id'],
            'loaned_at' => ['date'],
            'due_at' => ['date'],
        ]);

        $loan->update($validated);

        return response()->json($loan);
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return response()->noContent();
    }
}
