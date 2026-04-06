<?php

namespace App\Repositories\Eloquent\Organization;

use App\Models\Position;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;

class PositionRepository extends BaseRepository implements PositionRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(Position $model)
    {
        parent::__construct($model);
    }

    public function listByDepartment($departmentId)
    {
        return $this->model->where('department_id', $departmentId)->orWhere('department_id', null)->orderBy('level', 'asc')->get();
    }
}
