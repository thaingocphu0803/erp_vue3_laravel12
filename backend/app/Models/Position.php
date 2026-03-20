<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'name',
		'department_id',
		'description',
		'created_by',
		'updated_by',
		'status'
	];

	public function permissions() : BelongsToMany {
		return $this->belongsToMany(Permission::class,'permission_position', 'position_id', 'permission_id')
					->withTimestamps();
	}
}
