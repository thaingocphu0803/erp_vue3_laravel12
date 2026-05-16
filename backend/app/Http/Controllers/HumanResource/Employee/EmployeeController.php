<?php

namespace App\Http\Controllers\HumanResource\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResource\Employee\IndexEmployeeRequest as EmployeeIndexEmployeeRequest;
use App\Http\Requests\HumanResource\Employee\StoreEmployeeRequest;
use App\Http\Resources\HumanceResource\Employee\EmployeePaginateResource;
use App\Services\HumanResource\EmployeeService;
use App\Trait\FormatResponse;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
	use FormatResponse;

	public function __construct(
		protected EmployeeService $employeeService
	) {}
	public function create(StoreEmployeeRequest $storeEmployeeRequest)
	{
		$data = $storeEmployeeRequest->validated();
		$this->employeeService->create($data);

		$message = 'employee.alert.success.create';
		return $this->jsonResponse($message, Response::HTTP_CREATED);
	}

	public function index(EmployeeIndexEmployeeRequest $indexEmployeeRequest)
	{
		$data = $indexEmployeeRequest->validated();
		$employeePaginate = $this->employeeService->paginate($data);

		return EmployeePaginateResource::collection($employeePaginate);
	}
}
