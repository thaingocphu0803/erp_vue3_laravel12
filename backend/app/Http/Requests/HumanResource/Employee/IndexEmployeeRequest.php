<?php

namespace App\Http\Requests\HumanResource\Employee;

use App\Http\Requests\IndexCommonRequest;
use Illuminate\Validation\Rule;

class IndexEmployeeRequest extends IndexCommonRequest
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
		$parent = parent::rules();

		$rules = [
			'department_id' => ['bail', 'nullable', 'integer', Rule::exists('departments', 'id')],
			'position_id' => ['bail', 'nullable', 'integer', Rule::exists('positions', 'id')],
		];

		return array_merge($parent, $rules);
	}

	public function messages(): array
	{
		return parent::messages();
	}
}
