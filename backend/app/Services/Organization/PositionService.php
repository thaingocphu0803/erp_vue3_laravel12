<?php

namespace App\Services\Organization;

use App\Enum\Table;
use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use App\Trait\AutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionService
{
	use AutoGenerate;
	/**
	 * Create a new class instance.
	 */
	public function __construct(
		protected PositionRepositoryInterface $positionRepositoryInterface
	) {}

	public function create(array $data)
	{
		$data['created_by'] = Auth::id();

		if (is_null($data['code'])) {
			$data['code'] = $this->generateCode(Table::POSITION->value, 'POS');
		}

		return DB::transaction(function () use ($data) {
			$position = $this->positionRepositoryInterface->create($data);
			return $position;
		});
	}

	public function paginate(array $data)
	{
		$relations = ['creator', 'department', 'parent'];
		$counts = ['employees'];
		$positions = $this->positionRepositoryInterface->paginate($data, $relations, $counts);
		return $positions;
	}

	public function list()
	{
		$positions = $this->positionRepositoryInterface->list();
		return $positions;
	}

	public function listByDepartment(int $departmentId)
	{
		$positions = $this->positionRepositoryInterface->listByDepartment($departmentId);
		return $positions;
	}

	public function delete(int $id)
	{
		return DB::transaction(function () use ($id) {
			return $this->positionRepositoryInterface->delete($id);
		});
	}

	public function bulkDelete(array $data)
	{
		$ids = $data['ids'];
		return DB::transaction(function () use ($ids) {
			return $this->positionRepositoryInterface->bulkDelete($ids);
		});
	}

	public function bulkUpdateStatus(array $data)
	{
		$ids = $data['ids'];
		$payload = ['status' => $data['status']];

		return DB::transaction(function () use ($ids, $payload) {
			return $this->positionRepositoryInterface->bulkUpdate($ids, $payload);
		});
	}
}
