<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function create(array $payload);

	public function createWithPivote(array $payload, string $relation, array $pivotPayload);

	public function paginate(array $paginationPayload);
}
