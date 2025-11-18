<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BookRequest;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Book::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }


    public function store(BookRequest $request): JsonResponse
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

        $data = $request->validated();

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

    public function update(BookRequest $request, Book $book): JsonResponse
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

        $data = $request->validated();

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
