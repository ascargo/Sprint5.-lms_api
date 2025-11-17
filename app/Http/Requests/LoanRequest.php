<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'book_id'   => 'required|exists:books,id',
                'patron_id' => 'required|exists:patrons,id',
                'loaned_at' => 'required|date',
                'due_at'    => 'required|date',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'due_at' => 'required|date',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'book_id.required'   => 'A book ID is required.',
            'book_id.exists'     => 'The selected book does not exist.',
            'patron_id.required' => 'A patron ID is required.',
            'patron_id.exists'   => 'The selected patron does not exist.',
            'loaned_at.required' => 'The loan date is required.',
            'due_at.required'    => 'The due date is required.',
            'due_at.after_or_equal' => 'Due date must be the same or after the loan date.',
        ];
    }
}
