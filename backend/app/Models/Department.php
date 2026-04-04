<?php

namespace App\Models;

use App\Observers\System\NestedObserver;
use App\QueryScope\FilterableScope;
use App\Trait\NestedTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use HasFactory, SoftDeletes, FilterableScope, NestedTrait;

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
