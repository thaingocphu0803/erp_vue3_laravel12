<?php

namespace App\Services\HumanResource;

use App\Repositories\Interfaces\HumanResource\EmployeeRepositoryInterface;

class EmployeeService
{
	public function __construct(
		protected EmployeeRepositoryInterface $employeeRepository
	) {}
}
