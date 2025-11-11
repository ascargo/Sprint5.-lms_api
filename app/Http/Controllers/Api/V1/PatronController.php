<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patron;
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

        return response()->json($patron, 201);
    }

    public function show($id): JsonResponse
    {
        $patron = Patron::findOrFail($id);

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

        return response()->json($patron);
    }

    public function destroy(Patron $patron): JsonResponse
    {
        $patron->delete();

        return response()->json(null, 204);
    }
}
