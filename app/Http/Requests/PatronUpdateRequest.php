<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatronUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patron = $this->route('patron');

        return [
            'name'  => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
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
