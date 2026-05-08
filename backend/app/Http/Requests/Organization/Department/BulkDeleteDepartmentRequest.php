<?php

namespace App\Http\Requests\Organization\Department;

use App\Http\Requests\BulkCommonRequest;
use App\Trait\FormatResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;

class BulkDeleteDepartmentRequest extends BulkCommonRequest
{
    use FormatResponse;

    protected function getTableName(): string
    {
        return 'departments';
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

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        $messageCode = 'department.validate.bulkAction.delete';

        return $this->exceptionResponse($messageCode, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}
