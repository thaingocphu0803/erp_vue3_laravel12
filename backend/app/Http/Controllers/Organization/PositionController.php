<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Position\BulkDeletePositionRequest;
use App\Http\Requests\Organization\Position\BulkUpdateStatusPositionRequest;
use App\Http\Requests\Organization\Position\IndexPositionRequest;
use App\Http\Requests\Organization\Position\ListByDepartmentRequest;
use App\Http\Requests\Organization\Position\StorePositionRequest;
use App\Http\Resources\Organization\Position\PositionListResource;
use App\Http\Resources\Organization\Position\PositionPagnateResource;
use App\Models\Position;
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
		$this->positionService->create($data);

		$message = 'position.alert.success.create';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function index(IndexPositionRequest $indexPositionRequest)
	{
		$data = $indexPositionRequest->validated();
		$positions = $this->positionService->paginate($data);
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

		return PositionListResource::collection($positions)->response();
	}

	public function delete(Position $position)
	{
		$this->positionService->delete($position->id);

		$message = 'position.alert.success.delete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkDelete(BulkDeletePositionRequest $bulkDeletePositionRequest)
	{
		$data = $bulkDeletePositionRequest->validated();
		$this->positionService->bulkDelete($data);

		$message = 'position.alert.success.bulkDelete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkUpdateStatus(BulkUpdateStatusPositionRequest $bulkUpdateStatusPositionRequest)
	{
		$data = $bulkUpdateStatusPositionRequest->validated();
		$this->positionService->bulkUpdateStatus($data);

		$message = 'position.alert.success.bulkUpdateStatus';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}
}
