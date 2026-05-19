<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\AuthorRelationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
	use SoftDeletes, FilterableScope, AuthorRelationTrait;
	protected $searchable = [
		'name',
		'code'
	];

	protected $fillable = [
		'user_id',
		'code',
		'address',
		'phone_number',
		'gender',
		'birth_date',
		'avatar',
		'department_id',
		'position_id',
		'is_leader',
		'name',
		'ward_code',
		'province_code',
		'created_by',
		'updated_by',
	];

	public function scopeSearch(Builder $query, string|null $search)
	{
		$columns = $this->searchable;

		$query->when(!is_null($search), function ($q) use ($search, $columns) {
			$q->join('users', 'users.id', '=', 'employees.user_id')
				->where(function ($sq) use ($search, $columns) {
					foreach ($columns as $column) {
						if ($column === 'name') {
							$sq->orWhere('users.' . $column, 'LIKE', "%$search%");
						} else {
							$sq->orWhere($column, 'LIKE', "%$search%");
						}
					}
				});
		});

		return $query;
	}

	public function scopeSortOrder(Builder $query, array $sort)
	{
		$query->when(!is_null($sort['sortKey']) && !is_null($sort['sortOrder']), function ($q) use ($sort) {
			if ($sort['sortKey'] === 'name') {
				$q->join('users', 'users.id', '=', 'employees.user_id');
			}
			$q->orderBy($sort['sortKey'], $sort['sortOrder']);
		});

		return $query;
	}

	public function credential()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function department()
	{
		return $this->belongsTo(Department::class);
	}

	public function position()
	{
		return $this->belongsTo(Position::class);
	}

	public function ward()
	{
		return $this->belongsTo(Ward::class);
	}

	public function province()
	{
		return $this->belongsTo(Province::class);
	}
}
