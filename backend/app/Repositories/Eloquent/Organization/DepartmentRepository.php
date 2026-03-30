<?php

namespace App\Repositories\Eloquent\Organization;

use App\Models\Department;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }
}
