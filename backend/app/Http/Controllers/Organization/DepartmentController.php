<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Department\IndexDepartmentRequest;
use App\Http\Requests\Organization\Department\StoreDepartmentRequest;
use App\Trait\FormatResponse;
use App\Http\Resources\Organization\Deparment\DepartmentListResource;
use App\Http\Resources\Organization\Deparment\DepartmentPaginateResource;
use App\Services\Organization\DepartmentService;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
	use FormatResponse;

	public function __construct(
		protected DepartmentService $departmentService
	) {}

	public function create(StoreDepartmentRequest $storeDepartmentRequest)
	{
		$data = $storeDepartmentRequest->validated();
		$this->departmentService->create($data);

		$message = 'department.alert.success.create';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function index(IndexDepartmentRequest $indexDepartmentRequest)
	{
		$data = $indexDepartmentRequest->validated();

		$departments = $this->departmentService->paginate($data);

		if ($departments === false) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return DepartmentPaginateResource::collection($departments)->response();
	}

	public function list()
	{
		$departments = $this->departmentService->list();
		return DepartmentListResource::collection($departments)->response();
	}
}
