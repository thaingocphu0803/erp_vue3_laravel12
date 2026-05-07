<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;

use App\Trait\AutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
	use AutoGenerate;

	public function __construct(
		protected DepartmentRepositoryInterface $departmentRepositoryInterface,
	) {}

	public function create(array $data)
	{
		$data['created_by'] = Auth::id();

		if (is_null($data['code'])) {
			$data['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEPT');
		}

		return DB::transaction(function () use ($data) {
			$department = $this->departmentRepositoryInterface->create($data);
			return $department;
		});
	}

	public function paginate(array $data)
	{
		$relations = ['creator', 'leader'];
		$departments = $this->departmentRepositoryInterface->paginate($data, $relations);
		return $departments;
	}

	public function list()
	{
		$departments = $this->departmentRepositoryInterface->list();
		return $departments;
	}
}
