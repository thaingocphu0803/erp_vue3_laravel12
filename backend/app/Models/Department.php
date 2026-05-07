<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use App\Trait\NestedRelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Trait\AuthorRelationTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

	public function leader(): BelongsTo
	{
		return $this->belongsTo(User::class, 'leader_id');
	}

	public function employees(): HasMany
	{
		return $this->hasMany(Employee::class, 'department_id');
	}
}
