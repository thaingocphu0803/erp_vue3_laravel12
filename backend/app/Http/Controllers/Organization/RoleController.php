<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Role\IndexRoleRequest;
use App\Http\Requests\Organization\Role\StoreRoleRequest;
use App\Http\Resources\Organization\Role\RoleListResource;
use App\Http\Resources\Organization\Role\RolePaginateResource;
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

		if ($this->roleService->create($data) === false) {
			$message = 'role.alert.success.create';
			return $this->jsonResponse($message, Response::HTTP_OK);
		}

		$message = 'role.alert.error.create';
		return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexRoleRequest $indexRoleRequest)
	{
		$data = $indexRoleRequest->validated();

		$roles = $this->roleService->paginate($data);

		if ($roles === false) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return RolePaginateResource::collection($roles)->response();
	}

	public function list()
	{
		$roles = $this->roleService->list();

		if ($roles === false) {
			$message = 'role.alert.error.getList';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return RoleListResource::collection($roles)->response();
	}
}
