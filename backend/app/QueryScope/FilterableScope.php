<?php

namespace App\QueryScope;

use Illuminate\Database\Eloquent\Builder;

trait FilterableScope
{
    public function scopeFilter(Builder $query, array $filter){
		$query->when(!empty($filter), function ($q) use ($filter) {
			foreach($filter as $key => $val){
				if ($val !== null && $val !== '') {
					$q->where($key, $val);
				}
			}
		});

		return $query;
	}

	public function scopeSortOrder(Builder $query, array $sort){
		$query->when(!empty($sort['sortKey']) && !empty($sort['sortOrder']), function($q) use ($sort){
			$q->orderBy($sort['sortKey'], $sort['sortOrder']);
		});

		return $query;
	}

	public function scopeSearch(Builder $query, string|null $search){
		$query->when(!empty($search), function ($q) use ($search) {
			$q->where(function ($sq) use ($search) {
				$sq->where('name', 'LIKE', "%$search%")
				   ->orWhere('code', 'LIKE', "%$search%");
			});
		});

		return $query;
	}

}
