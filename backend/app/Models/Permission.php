<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
	protected $fillable = [
		'name',
		'slug',
		'module'
	];

	protected $casts = [
		'supported_scopes' => 'array'
	];

	public $timestamps = false;

	public function roles() : BelongsToMany {
		return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id')
			->withPivot('scope')
			->withTimestamps();
	}
}
