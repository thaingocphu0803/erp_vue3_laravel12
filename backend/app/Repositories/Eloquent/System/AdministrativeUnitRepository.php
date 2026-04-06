<?php

namespace App\Repositories\Eloquent\System;

use App\Models\Province;
use App\Models\Ward;
use App\Repositories\Interfaces\System\AdministrativeUnitRepositoryInterface;

class AdministrativeUnitRepository implements AdministrativeUnitRepositoryInterface
{
	public function provinces()
	{
		return Province::all();
	}

	public function wards(string $provinceCode)
	{
		return Ward::where('province_code', $provinceCode)->get();
	}
}
