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

		return DB::transaction(function () use ($data) {
			$this->positionRepositoryInterface->create($data);
			return true;
		});
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
		$positions = $this->positionRepositoryInterface->list();
		return $positions;
	}

	public function listByDepartment($departmentId)
	{
		$positions = $this->positionRepositoryInterface->listByDepartment($departmentId);
		return $positions;
	}
}
