<?php

namespace App\QueryScope;

use Illuminate\Database\Eloquent\Builder;

trait FilterableScope
{
	public function scopeWithRelation(Builder $query, string $relation)
	{
		$query->when(!empty($relation), function ($q) use ($relation) {
			$q->with($relation);
		});

		return $query;
	}

	public function scopeFilter(Builder $query, array $filter)
	{
		$query->when(!empty($filter), function ($q) use ($filter) {
			foreach ($filter as $key => $val) {
				if ($val !== null && $val !== '') {
					$q->where($key, $val);
				}
			}
		});

		return $query;
	}

	public function scopeSortOrder(Builder $query, array $sort)
	{
		$query->when(!is_null($sort['sortKey']) && !is_null($sort['sortOrder']), function ($q) use ($sort) {
			$q->orderBy($sort['sortKey'], $sort['sortOrder']);
		});

		return $query;
	}

	public function scopeSearch(Builder $query, string|null $search)
	{
		$columns = property_exists($this, 'searchable') ? $this->searchable : ['name'];

		$query->when(!is_null($search), function ($q) use ($search, $columns) {
			$q->where(function ($sq) use ($search, $columns) {
				foreach ($columns as $column) {
					$sq->orWhere($column, 'LIKE', "%$search%");
				}
			});
		});

		return $query;
	}
}
