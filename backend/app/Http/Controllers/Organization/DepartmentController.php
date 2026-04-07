<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommonRequest;
use App\Http\Requests\Organization\Department\StoreDepartmentRequest;
use App\Trait\HasResponse;
use App\Http\Resources\Organization\DepartmentResource;
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

		if ($this->departmentService->create($data)) {
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

		if (!$departments) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return DepartmentResource::collection($departments)->response();
	}
}
