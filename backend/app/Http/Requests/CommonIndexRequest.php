<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommonIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['bail', 'required','integer', 'min:1'],
			'itemsPerPage' => ['bail', 'required', 'integer', 'in:5,10,15,20'],
			'search' => ['nullable', 'string'],
			'sortOrder' => ['nullable', 'in:asc,desc'],
			'sortKey' => ['nullable', 'string']
        ];
    }
}
