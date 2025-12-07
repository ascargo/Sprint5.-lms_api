<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatronRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patron = $this->route('patron');

        return [
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('patrons', 'email')->ignore($patron?->id),
            ],
            'role' => [
                'sometimes',
                Rule::in(['admin', 'patron']),
            ],
        ];
    }
}
