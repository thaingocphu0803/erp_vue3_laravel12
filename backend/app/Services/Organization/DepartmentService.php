<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;

use App\Trait\HasAutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
	use HasAutoGenerate;

	public function __construct(
		protected DepartmentRepositoryInterface $departmentRepositoryInterface,
	) {}

	public function create(array $departmentPayload)
	{
		$departmentPayload['created_by'] = Auth::id();

		if (is_null($departmentPayload['code'])) {
			$departmentPayload['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEPT');
		}

		try {
			return DB::transaction(function () use ($departmentPayload) {
				$department = $this->departmentRepositoryInterface->create($departmentPayload);
				return $department;
			});
		} catch (\Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	public function paginate(array $paginationPayload)
	{
		try {
			$departments = $this->departmentRepositoryInterface->paginate($paginationPayload);
			return $departments;
		} catch (\Exception $e) {
			return false;
		}
	}
}
