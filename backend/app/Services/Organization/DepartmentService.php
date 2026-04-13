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

		try {
			return DB::transaction(function () use ($data) {
				$department = $this->departmentRepositoryInterface->create($data);
				return $department;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $data)
	{
		try {
			$departments = $this->departmentRepositoryInterface->paginate($data);
			return $departments;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function list()
	{
		try {
			$departments = $this->departmentRepositoryInterface->list();
			return $departments;
		} catch (\Exception $e) {
			return false;
		}
	}
}
