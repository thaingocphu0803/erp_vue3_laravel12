<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Department\BulkDeleteDepartmentRequest;
use App\Http\Requests\Organization\Department\BulkUpdateStatusDepartmentRequest;
use App\Http\Requests\Organization\Department\IndexDepartmentRequest;
use App\Http\Requests\Organization\Department\StoreDepartmentRequest;
use App\Trait\FormatResponse;
use App\Http\Resources\Organization\Deparment\DepartmentListResource;
use App\Http\Resources\Organization\Deparment\DepartmentPaginateResource;
use App\Models\Department;
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
		return DepartmentPaginateResource::collection($departments)->response();
	}

	public function list()
	{
		$departments = $this->departmentService->list();
		return DepartmentListResource::collection($departments)->response();
	}


	public function delete(Department $department)
	{
		$this->departmentService->delete($department->id);

		$message = 'department.alert.success.delete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkDelete(BulkDeleteDepartmentRequest $bulkDeleteDepartmentRequest)
	{
		$data = $bulkDeleteDepartmentRequest->validated();
		$this->departmentService->bulkDelete($data);

		$message = 'department.alert.success.bulkDelete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkUpdateStatus(BulkUpdateStatusDepartmentRequest $bulkUpdateStatusDepartmentRequest)
	{
		$data = $bulkUpdateStatusDepartmentRequest->validated();
		$this->departmentService->bulkUpdateStatus($data);

		$message = 'department.alert.success.bulkUpdateStatus';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}
}
