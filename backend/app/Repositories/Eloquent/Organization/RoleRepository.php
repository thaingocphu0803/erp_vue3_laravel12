<?php

namespace App\Repositories\Eloquent\Organization;

use App\Models\Role;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\Organization\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
	{
		parent::__construct($model);
	}
}
