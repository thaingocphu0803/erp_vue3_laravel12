<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
	public function create(array $payload, string $relation = '', array $pivotPayload = []);


	public function find(int $id, array $relations = []);

	public function paginate(array $paginationPayload, array $relations = [], array $count = []);

	public function list();

	public function update(int $id, array $payload);

	public function delete(int $id);
}
