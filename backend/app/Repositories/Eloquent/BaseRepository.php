<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
	public function __construct(
		protected Model $model
	) {}

	public function create(array $payload)
	{
		return $this->model->create($payload);
	}

	public function createWithPivote(array $payload, string $relation, array $pivotPayload)
	{
		$model = $this->create($payload);

		if (!empty($pivotPayload)) {
			$model->$relation()->attach($pivotPayload);
		}

		return $model;
	}

	public function update(int $id, array $payload)
	{
		return $this->model->where('id', $id)->update($payload);
	}

	public function paginate(array $paginationPayload, string $relation = '')
	{
		$defaultPerpage = 10;

		$filters = collect($paginationPayload)->except(['sortKey', 'sortOrder', 'search', 'itemsPerPage', 'page'])->toArray();

		$sort = [
			'sortKey' => $paginationPayload['sortKey'] ?? null,
			'sortOrder' => $paginationPayload['sortOrder'] ?? null,
		];

		$search = $paginationPayload['search'] ?? null;

		$itemsPerPage = $paginationPayload['itemsPerPage'] ?? $defaultPerpage;

		$result = $this->model
			->withRelation($relation)
			->filter($filters)
			->search($search)
			->sortOrder($sort)
			->paginate($itemsPerPage);

		return $result;
	}
}
