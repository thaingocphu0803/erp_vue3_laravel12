<?php

namespace App\Http\Requests\Organization\Department;

use App\Http\Requests\IndexCommonRequest;
use App\Trait\HasResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class IndexDepartmentRequest extends IndexCommonRequest
{
	use HasResponse;
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
		$commonRules = parent::rules();

		$departmentRules = [
			'status' => ['bail', 'nullable', 'string', 'in:A,X']
		];

		return array_merge($commonRules, $departmentRules);

    }

	public function failedValidation(Validator $validator)
	{
		$errors = $validator->errors()->messages();

		$message = 'common-list.alert.error.badFilter';

		return $this->exceptionResponse($message, JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $errors);
	}
}
