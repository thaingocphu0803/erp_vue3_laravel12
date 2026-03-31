<?php

namespace App\Http\Requests\Organization\Position;

use App\Http\Requests\StoreCommonRequest;
use Illuminate\Validation\Rule;

class StorePositionRequest extends StoreCommonRequest
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
		$parentRules = parent::rules();

		$positionRules =  [
			'department_id' => ['bail','nullable','integer', Rule::exists('departments', 'id')],
		];

		return array_merge($parentRules, $positionRules);
    }

	public function messages()
	{
		return [
            'name.required' => 'position.validate.name.required',
            'name.max'      => 'position.validate.name.max',
            'name.regex' => 'position.validate.name.noSpecialChars',
			'name.unique' => 'position.validate.name.unique',
			'department_id.integer' => 'position.validate.department_id.format',
			'department_id.exists' => 'position.validate.department_id.exists',
			'description.string' => 'position.validate.description.format'

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
