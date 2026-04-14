<?php

namespace App\Http\Requests\Auth;

use App\Rules\HashEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreatePasswordRequest extends FormRequest
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
			'password' => ['bail', 'required', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
		];
	}

	public function messages(): array
	{
		return [
			'id.required' => __('auth.alert.error.invalid_token'),
			'id.integer' => __('auth.alert.error.invalid_token'),
			'id.exists' => __('auth.alert.error.invalid_token'),
			'hash.required' => __('auth.alert.error.invalid_token'),
			'hash.string' => __('auth.alert.error.invalid_token'),
			'password.required' => 'auth.validate.password.required',
			'password.min' => 'auth.validate.password.min',
			'password.mixed' => 'auth.validate.password.hasUpperLetter',
			'password.numbers' => 'auth.validate.password.hasNumber',
			'password.symbols' => 'auth.validate.password.hasSpecialChar',
			'password.confirmed' => 'auth.validate.password.confirmed',
		];
	}
}
