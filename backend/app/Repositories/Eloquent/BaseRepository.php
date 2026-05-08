<?php

namespace App\Repositories\Eloquent;

use App\Enum\Status;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository implements BaseRepositoryInterface
{

	protected function withAggregates(Builder $query)
	{
		return $query;
	}

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

		$query = $this->model
			->withRelation($relations)
			->withCount($counts)
			->filter($filters)
			->search($search)
			->sortOrder($sort);

		$query = $this->withAggregates($query);

		return $query->paginate($itemsPerPage);
	}

	// List
	public function list()
	{
		return $this->model->where('status', Status::ACTIVE->value)->get();
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
