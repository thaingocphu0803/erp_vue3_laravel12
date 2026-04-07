<?php

namespace App\Http\Requests\HumanResource\Employee;

use App\Enum\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	public function prepareForValidation()
	{
		$this->merge([
			'gender' => strtolower($this->gender),
			'is_leader' => (bool) $this->is_leader,
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'avatar' => ['bail', 'nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
			'role_ids' => ['bail', 'required', 'array', 'min:1', 'distinct', Rule::exists('roles', 'id')->where('status', Status::ACTIVE->value)],
			'email' => ['bail', 'required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
			'position_id' => ['bail', 'required', Rule::exists('positions', 'id')->where('status', Status::ACTIVE->value)],
			'department_id' => ['bail', 'required', Rule::exists('departments', 'id')->where('status', Status::ACTIVE->value)],
			'is_leader' => ['boolean'],
			'name' => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u'],
			'code' => ['bail', 'nullable', 'max:20', Rule::unique('employees', 'code')->ignore($this->id)],
			'gender' => ['bail', 'required', 'in:male,female'],
			'birthdate' => ['bail', 'required', 'date_format:Y/m/d', 'before:today'],
			'phone_number' => ['bail', 'required', 'size:10', 'regex:/^0\d{9}$/', Rule::unique('employees', 'phone_number')->ignore($this->id)],
			'address' => ['bail', 'required', 'string'],
			'ward_code' => ['bail', 'required', Rule::exists('wards', 'code')],
			'province_code' => ['bail', 'required', Rule::exists('provinces', 'code')],
		];
	}

	public function messages(): array
	{
		return [
			'avatar.image' => 'employee.validate.avatar.image',
			'avatar.max' => 'employee.validate.avatar.size',
			'avatar.mimes' => 'employee.validate.avatar.format',
			'role_ids.required' => 'employee.validate.roleId.required',
			'role_ids.exists' => 'employee.validate.roleId.exists',
			'role_ids.atLeastOne' => 'employee.validate.roleId.atLeastOne',
			'role_ids.format' => 'employee.validate.roleId.format',
			'role_ids.distinct' => 'employee.validate.roleId.distinct',
			'email.required' => 'employee.validate.email.required',
			'email.email' => 'employee.validate.email.format',
			'email.unique' => 'employee.validate.email.unique',
			'position_id.required' => 'employee.validate.positionId.required',
			'position_id.exists' => 'employee.validate.positionId.exists',
			'department_id.required' => 'employee.validate.departmentId.required',
			'department_id.exists' => 'employee.validate.departmentId.exists',
			'is_leader.boolean' => 'employee.validate.isLeader.format',
			'name.required' => 'employee.validate.name.required',
			'name.max' => 'employee.validate.name.max',
			'name.regex' => 'employee.validate.name.noSpecialChars',
			'code.max' => 'employee.validate.code.max',
			'code.unique' => 'employee.validate.code.unique',
			'gender.required' => 'employee.validate.gender.required',
			'gender.in' => 'employee.validate.gender.format',
			'birthdate.required' => 'employee.validate.birthdate.required',
			'birthdate.date_format' => 'employee.validate.birthdate.format',
			'birthdate.before' => 'employee.validate.birthdate.before',
			'phone_number.required' => 'employee.validate.phoneNumber.required',
			'phone_number.size' => 'employee.validate.phoneNumber.size',
			'phone_number.regex' => 'employee.validate.phoneNumber.format',
			'phone_number.unique' => 'employee.validate.phoneNumber.unique',
			'address.required' => 'employee.validate.address.required',
			'address.string' => 'employee.validate.address.format',
			'ward_code.required' => 'employee.validate.wardCode.required',
			'ward_code.exists' => 'employee.validate.wardCode.exists',
			'province_code.required' => 'employee.validate.provinceCode.required',
			'province_code.exists' => 'employee.validate.provinceCode.exists',
		];
	}
}
