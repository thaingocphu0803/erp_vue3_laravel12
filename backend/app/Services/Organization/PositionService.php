<?php

namespace App\Services\Organization;

use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionService
{
	/**
	 * Create a new class instance.
	 */
	public function __construct(
		protected PositionRepositoryInterface $positionRepositoryInterface
	) {}

	public function create(array $data)
	{
		$data['created_by'] = Auth::id();

		try {
			return DB::transaction(function () use ($data) {
				$this->positionRepositoryInterface->create($data);
				return true;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $data)
	{
		try {
			$positions = $this->positionRepositoryInterface->paginate($data);
			return $positions;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function list()
	{
		try {
			$positions = $this->positionRepositoryInterface->list();
			return $positions;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function listByDepartment($departmentId)
	{
		try {
			$positions = $this->positionRepositoryInterface->listByDepartment($departmentId);
			return $positions;
		} catch (\Exception $e) {
			return false;
		}
	}
}
