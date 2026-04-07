<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class LookupByModelRequest extends FormRequest
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
			'model' => ['bail', 'string', 'required', 'in:departments,positions,roles']
		];
	}

	public function messages(): array
	{
		return [
			'model.*' => 'common-list.alert.error.badRequest',
		];
	}
}
