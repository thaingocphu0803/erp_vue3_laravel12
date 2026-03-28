<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'name',
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
}
