<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait NestedTrait
{
    /**
     * Get the parent of the model.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}
