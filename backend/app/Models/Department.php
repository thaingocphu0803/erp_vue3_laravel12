<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\NestedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use HasFactory, SoftDeletes, FilterableScope, NestedTrait;

	protected $searchable = [
		'name',
		'code'
	];

	protected $fillable = [
		'name',
		'code',
		'description',
		'parent_id',
		'created_by',
		'updated_by',
		'status',
		'path',
		'level'
	];
}
