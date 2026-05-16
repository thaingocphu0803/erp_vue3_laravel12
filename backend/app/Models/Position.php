<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\AuthorRelationTrait;
use App\Trait\NestedRelationTrait;
use Illuminate\Database\Eloquent\Builder;
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

	public function scopeFilter(Builder $query, array $filter)
	{
		if (array_key_exists('department_id', $filter)) {
			$departmentId = (int) $filter['department_id'];
			unset($filter['department_id']);

			$query->when(
				$departmentId === 0,
				fn($q) => $q->whereNull('department_id'),
				fn($q) => $q->where('department_id', $departmentId)
			);
		}

		foreach ($filter as $key => $val) {
			if ($val !== null && $val !== '') {
				$query->where($key, $val);
			}
		}

		return $query;
	}



	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'position_id');
	}
}
