<?php

namespace App\Repositories\Interfaces\System;

interface AdministrativeUnitRepositoryInterface
{
	public function provinces();

	public function wards(string $provinceCode);
}
