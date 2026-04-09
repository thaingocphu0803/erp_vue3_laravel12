<?php

namespace App\Http\Controllers\HumanResource\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResource\Employee\StoreEmployeeRequest;
use App\Services\HumanResource\EmployeeService;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
	use HasResponse;

	public function __construct(
		protected EmployeeService $employeeService
	) {}
	public function create(StoreEmployeeRequest $storeEmployeeRequest)
	{
		$data = $storeEmployeeRequest->validated();

		if ($this->employeeService->create($data) !== false) {
			$message = 'employee.alert.success.create';
			return $this->jsonResponse($message, JsonResponse::HTTP_OK);
		}

		$message = 'employee.alert.error.create';
		return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}
}
