<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(
		protected Model $model
	){}

	public function create(array $payload)
	{
		return $this->model->create($payload);
	}

	public function createWithPivote(array $payload, string $relation, array $pivotPayload){
		$model = $this->create($payload);

		if(!empty($pivotPayload)){
			$model->$relation()->attach($pivotPayload);
		}

		return $model;
	}

	public function paginate(array $paginationPayload){
		$defaultPerpage = 10;

		$filters = collect($paginationPayload)->except(['sortKey', 'sortOrder', 'search', 'itemsPerPage', 'page'])->toArray();

		$sort = [
			'sortKey' => $paginationPayload['sortKey'] ?? null,
			'sortOrder' => $paginationPayload['sortOrder'] ?? null,
		];

		$search = $paginationPayload['search'] ?? null;

		$itemsPerPage = $paginationPayload['itemsPerPage'] ?? $defaultPerpage;

		return $this->model
			->query()
			->filter($filters)
			->search($search)
			->sortOrder($sort)
			->paginate($itemsPerPage);
	}
}
