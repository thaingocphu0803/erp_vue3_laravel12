<?php

namespace App\Services\System;

use App\Repositories\Interfaces\System\NestedRepositoryInterface;

class NestedService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
		protected NestedRepositoryInterface $nestedRepositoryInterface
	){}

	public function findParentById(string $table, int|null $parent_id, array $columns){
		if(!empty($parent_id)) return null;

		return $this->nestedRepositoryInterface->findParentById($table, $parent_id, $columns);
	}

	public function makeLevel($parent){
		$default = 1;

		if(!empty($parent)){
			return $parent->level + $default;
		}

		return $default;
	}

	public function makePath($parent, int $model_id){
		$default = $model_id. '/';

		if(!empty($parent)){
			return $parent->path . $default;
		}

		return $default;
	}
}
