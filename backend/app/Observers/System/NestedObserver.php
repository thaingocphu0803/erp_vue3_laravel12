<?php

namespace App\Observers\System;

use Illuminate\Database\Eloquent\Model;

class NestedObserver
{
    /**
     * Handle the model "creating" event.
     */
    public function creating(Model $model): void
    {
        $parentId = $model->parent_id;

        if ($parentId) {
            $parent = $model::lockForUpdate()->find($parentId, ['id', 'level', 'path']);
            if ($parent) {
                $model->setRelation('parent', $parent);
                $model->level = $parent->level + 1;
                return;
            }
        }

        $model->level = 1;
    }


    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $parentId = $model->parent_id;
        $path = $model->id . '/';

        if ($parentId) {
            $parent = $model->relationLoaded('parent') ? $model->getRelation('parent') : $model::find($parentId, ['path']);
            if ($parent) {
                $path = $parent->path . $path;
            }
        }

        $model->path = $path;
        $model->saveQuietly();
    }
}
