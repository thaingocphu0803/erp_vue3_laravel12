<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\VerifyEmailCommonRequest;
use Illuminate\Validation\Rules\Password;

class CreatePasswordRequest extends VerifyEmailCommonRequest
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

		$createPasswordRules = [
			'password' => ['bail', 'required', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed']
		];

		return array_merge($parentRules, $createPasswordRules);
	}

	public function messages(): array
	{
		$parentMessages = parent::messages();

		$createPasswordMessages = [
			'password.required' => 'auth.validate.password.required',
			'password.min' => 'auth.validate.password.min',
			'password.mixed' => 'auth.validate.password.hasUpperLetter',
			'password.numbers' => 'auth.validate.password.hasNumber',
			'password.symbols' => 'auth.validate.password.hasSpecialChar',
			'password.confirmed' => 'auth.validate.password.confirmed',
		];

		return array_merge($parentMessages, $createPasswordMessages);
	}
}
