<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
	public function create(array $payload, string $relation = '', array $pivotPayload = []);

	public function update(int $id, array $payload);

	public function find(int $id, string $relation = '');

	public function paginate(array $paginationPayload, string $relation = '');

	public function list();
}
