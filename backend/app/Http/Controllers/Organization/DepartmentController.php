<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommonRequest;
use App\Http\Requests\Organization\Department\StoreDepartmentRequest;
use App\Trait\HasResponse;
use App\Http\Resources\Organization\Deparment\DepartmentListResource;
use App\Http\Resources\Organization\Deparment\DepartmentPaginateResource;
use App\Services\Organization\DepartmentService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DepartmentController extends Controller
{
	use HasResponse;

	public function __construct(
		protected DepartmentService $departmentService
	) {}

	public function create(StoreDepartmentRequest $storeDepartmentRequest)
	{
		$data = $storeDepartmentRequest->validated();

		if ($this->departmentService->create($data) !== false) {
			$message = 'department.alert.success.create';
			return $this->jsonResponse($message, JsonResponse::HTTP_OK);
		}

		$message = 'department.alert.error.create';
		return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexCommonRequest $indexCommonRequest)
	{
		$data = $indexCommonRequest->validated();

		$departments = $this->departmentService->paginate($data);

		if ($departments === false) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return DepartmentPaginateResource::collection($departments)->response();
	}

	public function list()
	{
		$departments = $this->departmentService->list();

		if ($departments === false) {
			$message = 'department.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return DepartmentListResource::collection($departments)->response();
	}
}
