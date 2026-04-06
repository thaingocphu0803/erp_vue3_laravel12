<?php

namespace App\Services\System;

use App\Repositories\Interfaces\System\AdministrativeUnitRepositoryInterface;

class AdministrativeUnitService
{
	public function __construct(
		protected AdministrativeUnitRepositoryInterface $administrativeUnitRepositoryInterface
	) {}

	public function provinces()
	{
		return $this->administrativeUnitRepositoryInterface->provinces();
	}

	public function wards(string $provinceCode)
	{
		return $this->administrativeUnitRepositoryInterface->wards($provinceCode);
	}
}
