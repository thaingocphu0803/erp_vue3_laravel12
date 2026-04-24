<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\NestedTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
	use SoftDeletes, FilterableScope, NestedTrait;

	protected $searchable = [
		'name',
		'code'
	];

	protected $fillable = [
		'name',
		'code',
		'department_id',
		'description',
		'created_by',
		'updated_by',
		'status',
		'path',
		'level',
		'parent_id'
	];
}
