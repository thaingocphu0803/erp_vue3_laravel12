<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait NestedRelationTrait
{
	/**
	 * Get the parent of the model.
	 */
	public function parent(): BelongsTo
	{
		return $this->belongsTo(static::class, 'parent_id');
	}

	public function children(): HasMany
	{
		return $this->hasMany(static::class, 'parent_id');
	}
}
