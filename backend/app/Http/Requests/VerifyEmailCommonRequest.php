<?php

namespace App\Http\Requests;

use App\Rules\HashEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyEmailCommonRequest extends FormRequest
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
			'id' => ['bail', 'required', 'integer', Rule::exists('users', 'id')],
			'hash' => ['bail', 'required', 'string', new HashEmailRule($this->id)],
		];
	}

	public function messages(): array
	{
		return [
			'id.*' => 'auth.validate.verify.invalidToken',
			'hash.*' => 'auth.validate.verify.invalidToken',
		];
	}
}
