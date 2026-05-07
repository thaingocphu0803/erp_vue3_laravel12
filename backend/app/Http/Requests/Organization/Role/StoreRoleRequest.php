<?php

namespace App\Http\Requests\Organization\Role;

use App\Http\Requests\StoreCommonRequest;
use App\Rules\PermissionItemRule;

class StoreRoleRequest extends StoreCommonRequest
{
	protected function getTableName(): string
	{
		return 'roles';
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
			'permissions' => ['bail', 'required', 'array', 'min:1', new PermissionItemRule],
		]);
	}

	public function messages()
	{
		$atLeastOne = 'role.validate.permissions.atLeastOne';

		return [
			'name.required' => 'role.validate.name.required',
			'name.max' => 'role.validate.name.max',
			'name.regex' => 'role.validate.name.noSpecialChars',
			'name.unique' => 'role.validate.name.unique',
			'code.max' => 'role.validate.code.max',
			'code.unique' => 'role.validate.code.unique',
			'description.string' => 'role.validate.description.format',
			'permissions.array' => 'role.validate.permissions.format',
			'permissions.min' => $atLeastOne,
			'permissions.required' => $atLeastOne
		];
	}
}
