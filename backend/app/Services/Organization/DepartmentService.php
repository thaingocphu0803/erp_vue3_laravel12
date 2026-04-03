<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Services\System\NestedService;
use App\Trait\HasAutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
	use HasAutoGenerate;

	public function __construct(
		protected DepartmentRepositoryInterface $departmentRepositoryInterface,
		protected NestedService $nestedService
	) {}

	public function create(array $departmentPayload)
	{
		$departmentPayload['created_by'] = Auth::id();

		if (is_null($departmentPayload['code'])) {
			$departmentPayload['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEPT');
		}

		try {
			return DB::transaction(function () use ($departmentPayload) {

				$parent_id = $departmentPayload['parent_id'];

				$columns =['level', 'path'];

				$parent = $this->nestedService->findParentById(Table::DEPARTMENT->value, $parent_id, $columns);

				$level =  $this->nestedService->makeLevel($parent);

				$departmentPayload['level'] = $level;


				$department = $this->departmentRepositoryInterface->create($departmentPayload);

				$path = $this->nestedService->makePath($parent, $department->id);

				$department->update(['path' => $path]);

				return true;
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
