<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
	public function __construct(
		protected Model $model
	) {}

	// Create
	public function create(array $payload, string $relation = '', array $pivotPayload = [])
	{
		$model = $this->model->create($payload);

		if (!empty($pivotPayload) && !empty($relation)) {
			$model->$relation()->attach($pivotPayload);
		}

		return $model;
	}

	// Find
	public function find(int $id, array $relations = [])
	{
		return $this->model->withRelation($relations)->find($id);
	}

	// Paginate
	public function paginate(array $paginationPayload, array $relations = [], array $counts = [])
	{
		$defaultPerpage = config('system.default_items_perpage');

		$filters = collect($paginationPayload)->except(['sortKey', 'sortOrder', 'search', 'itemsPerPage', 'page'])->toArray();

		$sort = [
			'sortKey' => $paginationPayload['sortKey'] ?? null,
			'sortOrder' => $paginationPayload['sortOrder'] ?? null,
		];

		$search = $paginationPayload['search'] ?? null;

		$itemsPerPage = $paginationPayload['itemsPerPage'] ?? $defaultPerpage;

		$result = $this->model
			->withRelation($relations)
			->withCount($counts)
			->filter($filters)
			->search($search)
			->sortOrder($sort)
			->paginate($itemsPerPage);

		return $result;
	}

	// List
	public function list()
	{
		return $this->model->all();
	}

	// Update
	public function update(int $id, array $payload)
	{
		return $this->model->where('id', $id)->update($payload);
	}

	// Delete
	public function delete(int $id)
	{
		return $this->model->where('id', $id)->delete();
	}
}
