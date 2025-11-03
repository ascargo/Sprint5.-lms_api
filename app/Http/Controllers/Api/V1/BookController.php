<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Book::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books',
            'year' => 'nullable|integer',
            'genre' => 'nullable|string',
            'collection' => 'nullable|string',
            'location' => 'nullable|string',
            'cover_path' => 'nullable|string',
        ]);

        $book = Book::create($data);

        return response()->json($book, 201);
    }
}
