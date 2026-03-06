<?php

namespace App\Models;

use App\QueryScope\FilterableScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes, FilterableScope;

	protected $fillable = [
		'name',
		'code',
		'description',
		'parent_id',
		'created_by',
		'status'
	];


	public function parent(): BelongsTo {
		return $this->belongsTo(self::class, 'parent_id');
	}
	public function children(): HasMany {
		return $this->hasMany(self::class, 'parent_id');
	}
}
