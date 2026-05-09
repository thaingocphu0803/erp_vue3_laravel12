<?php

namespace App\Http\Requests\Organization\Position;

use App\Http\Requests\BulkCommonRequest;
use App\Trait\FormatResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BulkUpdateStatusPositionRequest extends BulkCommonRequest
{
	use FormatResponse;

	protected function getTableName(): string
	{
		return 'positions';
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$parent = parent::rules();

		return array_merge($parent, [
			'status' => ['required', Rule::in(config('system.status_rules'))],
		]);
	}

	/**
	 * Get custom messages for validator errors.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		return [];
	}

	public function failedValidation(Validator $validator)
	{
		$errors = $validator->errors()->messages();

		$messageCode = 'position.validate.bulkAction.updateStatus';

		return $this->exceptionResponse($messageCode, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
	}
}
