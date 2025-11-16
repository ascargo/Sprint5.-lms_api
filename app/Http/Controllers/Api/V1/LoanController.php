<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Loan;
use App\Models\User;
use Laravel\Passport\Passport;

class LoanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Loan::with(['book','patron'])->get(),
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

        return response()->json([
            'data' => $loan->load(['book','patron']),
            'message' => 'Loan created successfully',
        ], 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        return response()->json([
            'data' => $loan->load(['book','patron']),
        ]);
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
            'data' => $loan->load(['book','patron']),
            'message' => 'Loan updated successfully',
        ]);
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return response()->noContent();
    }
}
