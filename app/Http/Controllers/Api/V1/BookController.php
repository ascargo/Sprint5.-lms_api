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
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);

        $books = Book::query()
            ->status($request->query('status'))
            ->author($request->query('author'))
            ->title($request->query('title'))
            ->genre($request->query('genre'))
            ->paginate($perPage);

        return $books;
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

    /**
     * Display the specified book.
     *
     * @urlParam book int required The ID of the book. Example: 1
     */
    public function show(Book $book): JsonResponse
    {
        return response()->json([
            'data' => $book,
        ]);
    }


    public function update(BookUpdateRequest $request, Book $book): JsonResponse
    {
        $book->update($request->validated());

        return response()->json([
            'data' => $book,
            'message' => 'Book updated successfully',
        ], 200);
    }


    /**
     * Remove the specified book.
     *
     * @urlParam book int required The ID of the book. Example: 1
     */
    public function destroy(Book $book): JsonResponse
    {
        if (app()->environment('scribe')) {
            $book = Book::first() ?? Book::factory()->create();
        }

        $book->delete();
        return response()->json(null, 204);
    }
}
