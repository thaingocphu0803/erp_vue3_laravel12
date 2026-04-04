<?php

namespace App\Services\System;

use App\Repositories\Interfaces\System\LookupRepositoryInterface;

class LookupService
{
	public function __construct(
		protected LookupRepositoryInterface $lookupRepositoryInterface
	) {}

	public function list(string $model)
	{
		try {
			$list = $this->lookupRepositoryInterface->getList($model);
			return $list;
		} catch (\Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
}
