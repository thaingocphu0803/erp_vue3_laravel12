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

	public function create(array $data)
	{
		$role = [
			'name' => $data['name'],
			'description' => $data['description'],
			'created_by' => Auth::id(),
		];

		$permissions = $data['permissions'];

		$newPermissions = $this->getNewPermissionPayload($permissions);

		try {
			return DB::transaction(function () use ($role, $newPermissions) {
				$relation = Table::PERMISSION->value;

				$this->roleRepositoryInterface->create($role, $relation, $newPermissions);

				return true;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $data)
	{
		try {
			$relation = 'creator';

			$roles =  $this->roleRepositoryInterface->paginate($data, $relation);
			return $roles;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function list()
	{
		try {
			$roles = $this->roleRepositoryInterface->list();
			return $roles;
		} catch (\Exception $e) {
			return false;
		}
	}

	private function getNewPermissionPayload(array $permissions)
	{

		$newPermissions = [];

		foreach ($permissions as $id => $scope) {
			$newPermissions[$id] = ['scope' => strtoupper($scope)];
		}

		return $newPermissions;
	}
}
