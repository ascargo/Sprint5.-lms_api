<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'author'     => 'required|string|max:255',
            'isbn'       => 'nullable|string|max:255',
            'year'       => 'nullable|integer',
            'genre'      => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:255',
            'location'   => 'nullable|string|max:255',
            'status'     => 'nullable|in:available,loaned,lost,reserved',
        ];
    }
}
