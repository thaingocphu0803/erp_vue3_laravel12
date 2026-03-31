<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes, FilterableScope;

	protected $fillable = [
		'name',
		'department_id',
		'description',
		'created_by',
		'updated_by',
		'status'
	];
}
