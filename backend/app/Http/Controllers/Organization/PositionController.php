<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Position\IndexPositionRequest;
use App\Http\Requests\Organization\Position\ListByDepartmentRequest;
use App\Http\Requests\Organization\Position\StorePositionRequest;
use App\Http\Resources\Organization\Position\PositionListResource;
use App\Http\Resources\Organization\Position\PositionPagnateResource;
use App\Services\Organization\PositionService;
use App\Trait\FormatResponse;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
	use FormatResponse;

	public function __construct(
		protected PositionService $positionService
	) {}

	public function create(StorePositionRequest $request)
	{
		$data = $request->validated();

		if ($this->positionService->create($data) !== false) {
			$message = 'position.alert.success.create';
			return $this->jsonResponse($message, Response::HTTP_OK);
		}

		$message = 'position.alert.error.create';
		return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexPositionRequest $indexPositionRequest)
	{
		$data = $indexPositionRequest->validated();

		$positions = $this->positionService->paginate($data);

		if ($positions === false) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return PositionPagnateResource::collection($positions)->response();
	}

	public function list()
	{
		$positions = $this->positionService->list();
		return PositionListResource::collection($positions)->response();
	}

	public function listByDepartment(ListByDepartmentRequest $listByDepartmentRequest)
	{
		$departmentId = $listByDepartmentRequest->validated('department_id');

		$positions = $this->positionService->listByDepartment($departmentId);

		if ($positions === false) {
			$message = 'position.alert.error.getTableData';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return PositionListResource::collection($positions)->response();
	}
}
