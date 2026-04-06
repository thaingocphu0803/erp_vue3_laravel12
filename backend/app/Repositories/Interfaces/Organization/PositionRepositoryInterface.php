<?php

namespace App\Repositories\Interfaces\Organization;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface PositionRepositoryInterface extends BaseRepositoryInterface
{
    public function listByDepartment($departmentId);
}
