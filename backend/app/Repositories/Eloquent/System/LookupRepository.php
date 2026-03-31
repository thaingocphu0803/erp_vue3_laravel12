<?php

namespace App\Repositories\Eloquent\System;

use App\Repositories\Interfaces\System\LookupRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LookupRepository implements LookupRepositoryInterface
{
	public function getList(string $model)
	{
		$list = DB::table($model)->get();
		return $list;
	}
}
