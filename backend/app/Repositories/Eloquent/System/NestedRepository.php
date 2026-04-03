<?php

namespace App\Repositories\Eloquent\System;

use App\Repositories\Interfaces\System\NestedRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NestedRepository implements NestedRepositoryInterface
{

	public function findParentById(string $table, int|null $parent_id, array $columns)
	{
		return DB::table($table)->select($columns)->lockForUpdate()->find($parent_id);
	}
}
