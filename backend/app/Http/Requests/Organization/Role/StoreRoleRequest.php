<?php

namespace App\Http\Requests\Organization\Role;

use App\Enum\Status;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PermissionItemRule;
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
			'name' => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u', Rule::unique('roles', 'name')->ignore($this->id)->where('status', Status::ACTIVE->value)],
			'code' => ['bail', 'nullable', 'max:20', Rule::unique('roles', 'code')->ignore($this->id)->where('status', Status::ACTIVE->value)],
			'description' => ['bail', 'nullable', 'string'],
			'permissions' => ['bail', 'required', 'array', 'min:1', new PermissionItemRule],
		];
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
