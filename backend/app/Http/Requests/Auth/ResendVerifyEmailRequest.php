<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\VerifyEmailCommonRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Trait\FormatResponse;

class ResendVerifyEmailRequest extends VerifyEmailCommonRequest
{
	use FormatResponse;
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
		return parent::rules();
	}

	public function messages(): array
	{
		return parent::messages();
	}

	public function failedValidation(Validator $validator)
	{
		$errors = $validator->errors()->messages();

		$messageCode = 'auth.validate.verify.invalidToken';

		return $this->exceptionResponse($messageCode, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
	}
}
