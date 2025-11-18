<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'sometimes|string|max:255',
            'author'     => 'sometimes|string|max:255',
            'isbn'       => 'sometimes|string|max:20|unique:books,isbn,' . $this->book->id,
            'year'       => 'sometimes|integer',
            'genre'      => 'sometimes|string|max:255',
            'collection' => 'sometimes|string|max:255',
            'location'   => 'sometimes|string|max:255',
            'cover_path' => 'sometimes|string|max:255',
            'status'     => 'sometimes|in:available,loaned,lost,reserved',
        ];
    }
}
