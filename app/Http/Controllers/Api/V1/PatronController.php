<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatronRequest;

class PatronController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Patron::paginate(10));
    }

    public function store(PatronRequest $request): JsonResponse
    {
        $data = $request->validated();

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

    public function update(PatronRequest $request, Patron $patron): JsonResponse
    {
        $data = $request->validated();

        $patron->update($data);

        return response()->json([
            'data' => $patron,
            'message' => 'Patron updated successfully',
        ]);
    }

    public function destroy(Patron $patron): JsonResponse
    {
        $patron->delete();

        return response()->json(null, 204);
    }
}
