<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommonRequest;
use App\Http\Requests\Organization\Role\StoreRoleRequest;
use App\Http\Resources\Organization\RoleResource;
use App\Services\Organization\RoleService;
use App\Trait\HasResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoleController extends Controller
{
	use HasResponse;

	public function __construct(
		protected RoleService $roleService
	) {}

	public function create(StoreRoleRequest $storeRoleRequest)
	{
		$validatedData =  $storeRoleRequest->validated();

		$rolePayload = [
			'name' => $validatedData['name'],
			'description' => $validatedData['description']
		];

		$permissionPayload = $validatedData['permissions'];

		if ($this->roleService->create($rolePayload, $permissionPayload)) {
			$message = 'role.alert.success.create';
			return $this->jsonResponse($message, JsonResponse::HTTP_OK);
		}

		$message = 'role.alert.error.create';
		return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexCommonRequest $indexCommonRequest)
	{
		$paginatePayload = $indexCommonRequest->validated();

		$roles = $this->roleService->paginate($paginatePayload);

		if (!$roles) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return RoleResource::collection($roles)->response();
	}
}
