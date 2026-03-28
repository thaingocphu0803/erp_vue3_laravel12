<?php

namespace App\Http\Requests\Organization\Role;

use App\Rules\PermissionItemRule;
use App\Rules\PermissionRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
			'name'      => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u', Rule::unique('roles', 'name')->ignore($this->id)],
			'permissions' => ['bail', 'required', 'array', 'min:1', 'distinct', new PermissionItemRule],
		];
	}

	public function messages()
	{
		$atLeastOne = 'role.validate.permissions.atLeastOne';

		return [
			'name.required' => 'role.validate.name.required',
			'name.max'      => 'role.validate.name.max',
			'name.regex' => 'role.validate.name.noSpecialChars',
			'name.unique' => 'role.validate.name.unique',
			'permissions.min' => $atLeastOne,
			'permissions.required' => $atLeastOne,
			'permissions.distinct' => 'role.validate.permissions.distinct',
			'permissions.array' => 'role.validate.permissions.format',
		];
	}
}
