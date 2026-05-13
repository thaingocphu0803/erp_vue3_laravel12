<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\AuthorRelationTrait;
use App\Trait\NestedRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
	use SoftDeletes, FilterableScope, NestedRelationTrait, AuthorRelationTrait;

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

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'position_id');
	}
}
