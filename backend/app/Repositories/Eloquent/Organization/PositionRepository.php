<?php

namespace App\Repositories\Eloquent\Organization;

use App\Enum\Status;
use App\Models\Position;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use App\Trait\BulkActionTrait;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PositionRepository extends BaseRepository implements PositionRepositoryInterface
{
	use BulkActionTrait;
	/**
	 * Create a new class instance.
	 */
	public function __construct(Position $model)
	{
		parent::__construct($model);
	}

	public function listByDepartment(int $departmentId)
	{
		return $this->model->where(function ($query) use ($departmentId) {
			$query->where('department_id', $departmentId)
				->orWhere('department_id', null);
		})->where('status', Status::ACTIVE->value)->orderBy('level', 'asc')->get();
	}

	#[Override]
	public function withAggregates(Builder $query)
	{
		return $query->withEmployeeCount();
	}
}
