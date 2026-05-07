<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class StoreCommonRequest extends FormRequest
{
	// get table name
	abstract protected function getTableName(): string;

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
			'name' => ['bail', 'required', 'max:100', 'regex:/^[\p{L}\p{M}\p{N}\s]+$/u', Rule::unique($this->getTableName(), 'name')->ignore($this->id)],
			'code' => ['bail', 'nullable', 'max:20', Rule::unique($this->getTableName(), 'code')->ignore($this->id)],
			'description' => ['bail', 'nullable', 'string'],
		];
	}
}
