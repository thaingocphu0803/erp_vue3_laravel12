<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\RoleRepositoryInterface;
use App\Trait\AutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleService
{
	use AutoGenerate;
	public function __construct(
		protected RoleRepositoryInterface $roleRepositoryInterface
	) {}

	public function create(array $data)
	{
		$role = $this->getRolePayload($data);

		$permissions = $data['permissions'];

		$newPermissions = $this->getNewPermissionPayload($permissions);

		return DB::transaction(function () use ($role, $newPermissions) {
			$relation = Table::PERMISSION->value;

			$role = $this->roleRepositoryInterface->create($role, $relation, $newPermissions);
			return $role;
		});
	}

	public function paginate(array $data)
	{
		$relations = ['creator:id,name'];
		$counts = ['users'];

		$roles =  $this->roleRepositoryInterface->paginate($data, $relations, $counts);
		return $roles;
	}

	public function list()
	{
		$roles = $this->roleRepositoryInterface->list();
		return $roles;
	}

	private function getNewPermissionPayload(array $permissions)
	{

		$newPermissions = [];

		foreach ($permissions as $id => $scope) {
			$newPermissions[$id] = ['scope' => strtoupper($scope)];
		}

		return $newPermissions;
	}

	private function getRolePayload(array $data)
	{
		$code = !is_null($data['code']) ? $data['code'] : $this->generateCode(Table::ROLE->value, 'ROLE');

		return [
			'code' => $code,
			'name' => $data['name'],
			'description' => $data['description'],
			'created_by' => Auth::id(),
		];
	}
}
