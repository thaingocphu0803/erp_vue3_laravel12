<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Trait\HasAutoCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
	use HasAutoCode;

	public function __construct(
		protected DepartmentRepositoryInterface $departmentRepositoryInterface
	) {}

	public function create(array $departmentPayload)
	{
		$departmentPayload['created_by'] = Auth::id();

		if (is_null($departmentPayload['code'])) {
			$departmentPayload['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEPT');
		}

		try {
			return DB::transaction(function () use ($departmentPayload) {
				$this->departmentRepositoryInterface->create($departmentPayload);
				return true;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $paginationPayload){
		try{
			return $this->departmentRepositoryInterface->paginate($paginationPayload);
		}catch(\Exception $e){
			return false;
		}
	}
}
