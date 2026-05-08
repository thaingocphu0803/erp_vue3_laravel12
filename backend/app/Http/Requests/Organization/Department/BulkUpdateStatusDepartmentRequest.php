<?php

namespace App\Http\Requests\Organization\Department;

use App\Http\Requests\BulkCommonRequest;
use App\Trait\FormatResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BulkUpdateStatusDepartmentRequest extends BulkCommonRequest
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
        $parent = parent::rules();

        return array_merge($parent, [
            'status' => ['required', Rule::in(config('system.status_rules'))],
        ]);
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        $messageCode = 'department.validate.bulkAction.updateStatus';

        return $this->exceptionResponse($messageCode, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}
