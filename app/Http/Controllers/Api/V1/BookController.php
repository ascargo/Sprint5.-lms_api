<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookUpdateRequest;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $books = Book::query()
            ->status($request->query('status'))
            ->author($request->query('author'))
            ->title($request->query('title'))
            ->genre($request->query('genre'))
            ->get();

        return response()->json(['data' => $books]);
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

    public function update(BookUpdateRequest $request, Book $book): JsonResponse
    {
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
