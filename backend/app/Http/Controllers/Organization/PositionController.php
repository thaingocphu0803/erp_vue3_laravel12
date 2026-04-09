<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommonRequest;
use App\Http\Requests\Organization\Position\ListByDepartmentRequest;
use App\Http\Requests\Organization\Position\StorePositionRequest;
use App\Http\Resources\Lookup\DepartmentLookupResource;
use App\Http\Resources\Organization\PositionResource;
use App\Services\Organization\PositionService;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
	use HasResponse;

	public function __construct(
		protected PositionService $positionService
	) {}

	public function create(StorePositionRequest $request)
	{
		$data = $request->validated();

		if ($this->positionService->create($data)) {
			$message = 'position.alert.success.create';
			return $this->jsonResponse($message, JsonResponse::HTTP_OK);
		}

		$message = 'position.alert.error.create';
		return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexCommonRequest $indexCommonRequest)
	{
		$data = $indexCommonRequest->validated();

		$positions = $this->positionService->paginate($data);

		if (!$positions) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return PositionResource::collection($positions)->response();
	}

	public function listByDepartment(ListByDepartmentRequest $listByDepartmentRequest)
	{
		$departmentId = $listByDepartmentRequest->validated('department_id');

		$positions = $this->positionService->listByDepartment($departmentId);

		if (!$positions) {
			$message = 'position.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return DepartmentLookupResource::collection($positions)->response();
	}
}
