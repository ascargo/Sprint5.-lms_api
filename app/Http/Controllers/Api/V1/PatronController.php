<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatronRequest;
use App\Http\Requests\PatronUpdateRequest;
use Illuminate\Validation\Rule;

class PatronController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = (int) request()->query('per_page', 100);
        $perPage = max(1, min($perPage, 1000));

        $query = Patron::query();

        if ($search = request()->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = request()->query('role')) {
            $query->where('role', $role);
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(PatronRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['role'] = $data['role'] ?? 'patron';

        $patron = Patron::create($data);

        return response()->json([
            'data' => $patron,
            'message' => 'Patron created successfully',
        ], 201);
    }

    public function showMe(): JsonResponse
    {
        $user = auth()->user();
        $patron = Patron::find($user->id);

        if (!$patron) {
            return response()->json(['message' => 'Patron profile not found'], 404);
        }

        return response()->json(['data' => $patron]);
    }

    public function show(Patron $patron): JsonResponse
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $user->id !== $patron->id) {
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

    public function updateMe(Request $request): JsonResponse
    {
        $user = auth()->user();
        $patron = Patron::find($user->id);

        if (!$patron) {
            return response()->json(['message' => 'Patron profile not found'], 404);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('patrons', 'email')->ignore($patron->id),
            ],
        ]);

        $patron->update($validated);

        return response()->json([
            'data' => $patron,
            'message' => 'Profile updated successfully',
        ]);
    }

    public function destroy(Patron $patron): JsonResponse
    {
        $patron->delete();

        return response()->json(null, 204);
    }
}
