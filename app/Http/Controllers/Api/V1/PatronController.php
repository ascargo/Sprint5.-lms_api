<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatronController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Patron::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patrons,email',
        ]);

        $patron = Patron::create($data);

        return response()->json([
            'data' => $patron,
            'message' => 'Patron created successfully',
        ], 201);
    }

    public function show(Patron $patron): JsonResponse
    {
        return response()->json([
            'data' => $patron,
        ]);
    }

    public function update(Request $request, Patron $patron): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patrons,email,' . $patron->id,
        ]);

        $patron->update($data);

        return response()->json([
            'data' => $patron,
            'message' => 'Patron updated succesfully',
        ]);
    }

    public function destroy(Patron $patron)
    {
        $patron->delete();

        return response()->noContent();
    }
}
