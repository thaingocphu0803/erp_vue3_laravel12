<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListByProvinceRequest extends FormRequest
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
			'province_code' => $this->route('provinceCode'),
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
			'province_code' => ['bail', 'required', 'integer', Rule::exists('provinces', 'code')],
		];
	}

	public function messages(): array
	{
		return [
			'province_code.*' => 'employee.validate.wardCode.provinceInvalid',
		];
	}
}
