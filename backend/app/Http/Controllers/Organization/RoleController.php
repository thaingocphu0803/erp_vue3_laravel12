<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Role\StoreRoleRequest;
use App\Models\Role;
use App\Trait\HasResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoleController extends Controller
{
	use HasResponse;
	public function create(StoreRoleRequest $request)
	{
		$rolePayload = $request->only(['name', 'description']);
		$rolePayload['created_by'] = Auth::id();

		$permissions = $request->input('permissions');

		$permissionPayload = [];

		foreach ($permissions as $id => $scope) {
			$permissionPayload[$id] = ['scope' => $scope];
		}

		try {
			return DB::transaction(function () use ($permissionPayload, $rolePayload) {
				$role = Role::create($rolePayload);
				$role->permissions()->attach($permissionPayload);

				$message = 'role.alert.success.create';

				return $this->jsonResponse($message, JsonResponse::HTTP_OK);
			});
		} catch (\Exception $e) {
			$message = 'role.alert.error.create';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
