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

    public function show($id): JsonResponse
    {
        $book = Book::findOrFail($id);

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
            //'status_id' => 'exists:book_statuses,id', // enable if needed
        ]);

        $book->update($data);

        return response()->json($book);
    }

    public function destroy(Book $book): \Illuminate\Http\JsonResponse
    {
        $book->delete();
        return response()->json(null, 204);
    }
}
