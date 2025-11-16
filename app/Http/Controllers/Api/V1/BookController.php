<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
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

        return response()->json([
            'data' => $book,
            'message' => 'Book created successfully',
    ], 201);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json([
            'data' => $book,
        ]);
    }

    public function update(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'year' => 'nullable|integer',
            'genre' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'cover_path' => 'nullable|string|max:255',
        ]);

        $book->update($data);

        return response()->json([
            'data' => $book,
            'message' => 'Book updated successfully',
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();
        return response()->json(null, 204);
    }
}
