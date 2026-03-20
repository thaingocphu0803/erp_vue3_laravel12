<?php

namespace App\Http\Requests\Organization\Position;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePositionRequest extends FormRequest
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
        	'name' => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u', Rule::unique('positions', 'name')->ignore($this->id)],
			'department_id' => ['bail','nullable','integer', Rule::exists('departments', 'id')],
			'permissions' => ['bail', 'required', 'array', 'min:1', 'distinct'],
			'permissions.*' => ['bail', 'integer', Rule::exists('permissions', 'id')]
        ];
    }

	public function messages()
	{
		$atLeastOne = 'position.validate.permissions.atLeastOne';
		$format = 'position.validate.permissions.format';

		return [
            'name.required' => 'position.validate.name.required',
            'name.max'      => 'position.validate.name.max',
            'name.regex' => 'position.validate.name.noSpecialChars',
			'name.unique' => 'position.validate.name.unique',
			'department_id.integer' => 'position.validate.department_id.format',
			'department_id.exists' => 'position.validate.department_id.exists',
			'permissions.min' => $atLeastOne,
			'permissions.required' => $atLeastOne,
			'permissions.distinct' => 'position.validate.permissions.distinct',
			'permissions.array' => $format,
			'permissions.*.integer' => $format,
			'permissions.*.exists' => 'position.validate.permissions.exists',
		];
	}

	public function withValidator($validator){
		$validator->after(function ($validator){
			$errors = $validator->errors();

			$allChildErrors =  $errors->get('permissions.*');

			if(!empty($allChildErrors)){
				$firstMessage = collect($allChildErrors)->flatten()->first();

				$errors->add('permissions', $firstMessage);
			}
		});
	}
}
