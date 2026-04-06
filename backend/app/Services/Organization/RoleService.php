<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\RoleRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleService
{
	public function __construct(
		protected RoleRepositoryInterface $roleRepositoryInterface
	) {}

	public function create(array $rolePayload, array $permissionPayload)
	{
		$rolePayload['created_by'] = Auth::id();

		$newPermissionPayload = $this->getNewPermissionPayload($permissionPayload);

		try {
			return DB::transaction(function () use ($rolePayload, $newPermissionPayload) {
				$relation = Table::PERMISSION->value;

				$this->roleRepositoryInterface->createWithPivote($rolePayload, $relation, $newPermissionPayload);

				return true;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $paginationPayload)
	{
		try {
			$relation = 'creator';

			$roles =  $this->roleRepositoryInterface->paginate($paginationPayload, $relation);
			return $roles;
		} catch (\Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	private function getNewPermissionPayload(array $permissionPayload)
	{

		$newPermissionPayload = [];

		foreach ($permissionPayload as $id => $scope) {
			$newPermissionPayload[$id] = ['scope' => strtoupper($scope)];
		}

		return $newPermissionPayload;
	}
}
