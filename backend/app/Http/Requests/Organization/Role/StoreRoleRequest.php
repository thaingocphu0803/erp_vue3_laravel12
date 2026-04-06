<?php

namespace App\Http\Requests\Organization\Role;

use App\Http\Requests\StoreCommonRequest;
use App\Rules\PermissionItemRule;

class StoreRoleRequest extends StoreCommonRequest
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

		$roleRules =  [
			'permissions' => ['bail', 'required', 'array', 'min:1', new PermissionItemRule],
		];

		return array_merge($parentRules, $roleRules);
	}

	public function messages()
	{
		$atLeastOne = 'role.validate.permissions.atLeastOne';

		return [
			'name.required' => 'role.validate.name.required',
			'name.max'      => 'role.validate.name.max',
			'name.regex' => 'role.validate.name.noSpecialChars',
			'name.unique' => 'role.validate.name.unique',
			'description.string' => 'role.validate.description.format',
			'permissions.array' => 'role.validate.permissions.format',
			'permissions.min' => $atLeastOne,
			'permissions.required' => $atLeastOne
		];
	}
}
