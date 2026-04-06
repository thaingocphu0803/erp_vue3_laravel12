<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
	protected $primaryKey = 'code';

	public $incrementing = false;

	protected $keyType = 'string';

	public function province()
	{
		return $this->belongsTo(Province::class, 'province_code', 'code');
	}
}
