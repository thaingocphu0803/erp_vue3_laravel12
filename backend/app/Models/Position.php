<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\AuthorRelationTrait;
use App\Trait\NestedRelationTrait;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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


	#[Scope]
	protected function withEmployeeCount(Builder $query): Builder
	{
		return $query->addSelect([
			'users_count' => DB::table('employees')
				->selectRaw('COUNT(*)')
				->whereNull('employees.deleted_at')
				->whereIn('employees.position_id', function ($subQuery) {
					$subQuery->select('id')
						->from('positions as pos')
						->whereNull('pos.deleted_at')
						->where('pos.status', 'A')
						->where('pos.path', 'LIKE', DB::raw("CONCAT(positions.path, '%')"))
						->orWhereColumn('pos.id', 'positions.id');
				}),
		]);
	}

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}
}
