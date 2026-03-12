<?php

namespace App\Http\Requests\Hr\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
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
            'name'      => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u'],
            'code'      => ['bail', 'nullable', 'max:20', Rule::unique('departments','code')->ignore($this->id)],
			'parent_id' => ['bail','nullable','integer', Rule::exists('departments', 'id')]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'department.validate.name.required',
            'name.max'      => 'department.validate.name.max',
            'name.regex' => 'department.validate.name.noSpecialChars',
            'code.max'      => 'department.validate.code.max',
			'code.unique' =>  'department.validate.code.unique',
			'parent_id.exists' => 'department.validate.parent_id.exists'
        ];
    }

}
