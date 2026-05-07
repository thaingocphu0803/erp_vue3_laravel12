<?php

namespace App\Http\Requests\Organization\Department;

use App\Enum\Status;
use App\Http\Requests\StoreCommonRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends StoreCommonRequest
{
	protected function getTableName(): string
	{
		return 'departments';
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$parentRules = parent::rules();

		return array_merge($parentRules, [
			'parent_id' => ['bail', 'nullable', 'integer', Rule::exists('departments', 'id')->where('status', Status::ACTIVE->value)],
		]);
	}

	public function messages(): array
	{
		return [
			'name.required' => 'department.validate.name.required',
			'name.max' => 'department.validate.name.max',
			'name.regex' => 'department.validate.name.noSpecialChars',
			'name.unique' => 'department.validate.name.unique',
			'code.max' => 'department.validate.code.max',
			'code.unique' => 'department.validate.code.unique',
			'parent_id.integer' => 'department.validate.parent_id.format',
			'parent_id.exists' => 'department.validate.parent_id.exists',
			'description.string' => 'department.validate.description.format'
		];
	}
}
