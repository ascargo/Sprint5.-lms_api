<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatronRequest;
use App\Http\Requests\PatronUpdateRequest;

class PatronController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);
        $search = $request->query('search');

        $patrons = Patron::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage);

        return response()->json($patrons);
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
        $user = auth()->user();

        if ($user->role !== 'admin' && $user->patron?->id !== $patron->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['data' => $patron]);
    }

    public function update(PatronUpdateRequest $request, Patron $patron): JsonResponse
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

    public function me(Request $request): JsonResponse
    {
        $patron = $request->user()?->patron;

        if (! $patron) {
            return response()->json(['message' => 'Patron profile not found'], 404);
        }

        return response()->json(['data' => $patron]);
    }
}
