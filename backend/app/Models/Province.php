<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
	protected $primaryKey = 'code';

	public $incrementing = false;

	protected $keyType = 'string';

	public function wards()
	{
		return $this->hasMany(Ward::class, 'province_code', 'code');
	}
}
