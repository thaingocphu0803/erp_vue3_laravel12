<?php

namespace App\Http\Requests\Organization\Position;

use App\Enum\Status;
use App\Http\Requests\StoreCommonRequest;
use Illuminate\Validation\Rule;

class StorePositionRequest extends StoreCommonRequest
{
	protected function getTableName(): string
	{
		return 'positions';
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
			'department_id' => ['bail', 'nullable', 'integer', Rule::exists('departments', 'id')->where('status', Status::ACTIVE->value)],
			'parent_id' => ['bail', 'nullable', 'integer', Rule::exists('positions', 'id')->where('status', Status::ACTIVE->value)],
		]);
	}

	public function messages()
	{
		return [
			'name.required' => 'position.validate.name.required',
			'name.max'      => 'position.validate.name.max',
			'name.regex' => 'position.validate.name.noSpecialChars',
			'name.unique' => 'position.validate.name.unique',
			'code.max' => 'position.validate.code.max',
			'code.unique' => 'position.validate.code.unique',
			'department_id.integer' => 'position.validate.department_id.format',
			'department_id.exists' => 'position.validate.department_id.exists',
			'parent_id.integer' => 'position.validate.parent_id.format',
			'parent_id.exists' => 'position.validate.parent_id.exists',
			'description.string' => 'position.validate.description.format'

		];
	}

	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			$errors = $validator->errors();

			$allChildErrors =  $errors->get('permissions.*');

			if (!empty($allChildErrors)) {
				$firstMessage = collect($allChildErrors)->flatten()->first();

				$errors->add('permissions', $firstMessage);
			}
		});
	}
}
