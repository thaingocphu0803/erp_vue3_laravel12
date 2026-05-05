<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
	use SoftDeletes, FilterableScope;

	protected $searchable = [
		'name',
		'code'
	];

	protected $fillable = [
		'name',
		'code',
		'description',
		'status',
		'created_by',
		'updated_by'
	];

	public function permissions(): BelongsToMany
	{
		return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id')
			->withPivot('scope')
			->withTimestamps();
	}

	public function creator(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
