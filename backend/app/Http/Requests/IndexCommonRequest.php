<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Trait\FormatResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class IndexCommonRequest extends FormRequest
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
		return [
			'page' => ['bail', 'required', 'integer', 'min:1'],
			'itemsPerPage' => ['bail', 'required', 'integer', Rule::in(config('system.items_per_page_rules'))],
			'search' => ['nullable', 'string'],
			'sortOrder' => ['nullable', Rule::in(config('system.sort_order_rules'))],
			'sortKey' => ['nullable', 'string'],
			'status' => ['bail', 'nullable', 'string', Rule::in(config('system.status_rules'))]
		];
	}

	public function messages(): array
	{
		return [];
	}

	public function failedValidation(Validator $validator)
	{
		$errors = $validator->errors()->messages();

		$messageCode = 'common.error.badRequest';

		return $this->exceptionResponse($messageCode, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
	}
}
