<?php

namespace App\Repositories\Eloquent\HumanResource;

use App\Models\Employee;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\HumanResource\EmployeeRepositoryInterface;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
	/**
	 * Create a new class instance.
	 */
	public function __construct(Employee $model)
	{
		parent::__construct($model);
	}
}
