<?php

namespace App\Repositories\Interfaces\Organization;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\BulkActionRepositoryInterface;

interface PositionRepositoryInterface extends BaseRepositoryInterface, BulkActionRepositoryInterface
{
    public function listByDepartment(int $departmentId);
}
