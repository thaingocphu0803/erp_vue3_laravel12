<?php

namespace App\Http\Requests\Auth;

use App\Trait\HandleResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginRequest extends FormRequest
{
    use HandleResponse;

    // validation should stop after the first rule failure.
    protected $stopOnFirstFailure = true;

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
            'email' => ['required', 'email'],
            'password' => ['bail','required', Password::min(8)->mixedCase(true)->numbers()->symbols()],
        ];
    }

    // override failedValidation from FromRequest class
    protected function failedValidation(Validator $validator)
    {

        $message = 'auth.alert.error.invalidAuth';

        $errors = $validator->errors()->toArray();

        $this->exceptionResponse($message, $errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
