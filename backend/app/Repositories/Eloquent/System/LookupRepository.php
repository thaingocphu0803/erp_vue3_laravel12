<?php

namespace App\Repositories\Eloquent\System;

use App\Enum\Status;
use App\Repositories\Interfaces\System\LookupRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LookupRepository implements LookupRepositoryInterface
{
	public function getList(string $model)
	{
		$list = DB::table($model)->where('status', Status::ACTIVE->value)->get();
		return $list;
	}
}
