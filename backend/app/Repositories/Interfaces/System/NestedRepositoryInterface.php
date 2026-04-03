<?php

namespace App\Repositories\Interfaces\System;

interface NestedRepositoryInterface
{
    public function findParentById(string $table, int|null $parent_id, array $columns);
}
