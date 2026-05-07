<?php

namespace App\Trait;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait AuthorRelationTrait
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
