<?php

namespace App\Services\HumanResource;

use App\Repositories\Interfaces\HumanResource\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
	public function __construct(
		protected EmployeeRepositoryInterface $employeeRepository
	) {}

	public function create(array $data)
	{
		try {
			DB::transaction(function () use ($data) {
				$employee = $this->employeeRepository->create($data);
			});
		} catch (\Exception $e) {
			return false;
		}
	}
}
