<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Role\BulkDeleteRoleRequest;
use App\Http\Requests\Organization\Role\BulkUpdateStatusRoleRequest;
use App\Http\Requests\Organization\Role\IndexRoleRequest;
use App\Http\Requests\Organization\Role\StoreRoleRequest;
use App\Http\Resources\Organization\Role\RoleListResource;
use App\Http\Resources\Organization\Role\RolePaginateResource;
use App\Models\Role;
use App\Services\Organization\RoleService;
use App\Trait\FormatResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
	use FormatResponse;

	public function __construct(
		protected RoleService $roleService
	) {}

	public function create(StoreRoleRequest $storeRoleRequest)
	{
		$data =  $storeRoleRequest->validated();
		$this->roleService->create($data);

		$message = 'role.alert.success.create';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function index(IndexRoleRequest $indexRoleRequest)
	{
		$data = $indexRoleRequest->validated();
		$roles = $this->roleService->paginate($data);
		return RolePaginateResource::collection($roles)->response();
	}

	public function list()
	{
		$roles = $this->roleService->list();
		return RoleListResource::collection($roles)->response();
	}

	public function delete(Role $role)
	{
		$roleId = $role->id;
		$this->roleService->delete($roleId);

		$message = 'role.alert.success.delete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkDelete(BulkDeleteRoleRequest $bulkDeleteRoleRequest)
	{
		$data = $bulkDeleteRoleRequest->validated();
		$this->roleService->bulkDelete($data);

		$message = 'role.alert.success.bulkDelete';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function bulkUpdateStatus(BulkUpdateStatusRoleRequest $bulkUpdateStatusRoleRequest)
	{
		$data = $bulkUpdateStatusRoleRequest->validated();
		$this->roleService->bulkUpdateStatus($data);

		$message = 'role.alert.success.bulkUpdateStatus';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}
}
