<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\NestedRelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Trait\AuthorRelationTrait;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
	use HasFactory, SoftDeletes, FilterableScope, NestedRelationTrait, AuthorRelationTrait;

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

	#[Scope]
	protected function withEmployeeCount(Builder $query): Builder
	{
		return $query->addSelect([
			'users_count' => DB::table('employees')
				->selectRaw('COUNT(*)')
				->whereNull('employees.deleted_at')
				->whereIn('employees.department_id', function ($subQuery) {
					$subQuery->select('id')
						->from('departments as dept')
						->whereNull('dept.deleted_at')
						->where('dept.status', 'A')
						->where('dept.path', 'LIKE', DB::raw("CONCAT(departments.path, '%')"))
						->orWhereColumn('dept.id', 'departments.id');
				}),
		]);
	}

	public function leader(): BelongsTo
	{
		return $this->belongsTo(User::class, 'leader_id');
	}

	public function employees(): HasMany
	{
		return $this->hasMany(Employee::class, 'department_id');
	}
}
