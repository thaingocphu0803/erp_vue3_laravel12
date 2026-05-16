<?php

namespace App\Http\Requests\Organization\Position;

use App\Http\Requests\IndexCommonRequest;
use Illuminate\Validation\Rule;

class IndexPositionRequest extends IndexCommonRequest
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
		$parent = parent::rules();

		$department = ['bail', 'nullable', 'integer'];

		if (intval($this->department_id) > 0) {
			$department[] = Rule::exists('departments', 'id');
		}

		return array_merge($parent, [
			'department_id' => $department
		]);
	}

	public function messages(): array
	{
		return parent::messages();
	}
}
