<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Model $model
 */

trait BulkActionTrait
{
	public function bulkDelete(array $ids)
	{
		return $this->model->whereIn('id', $ids)->delete();
	}

	public function bulkUpdate(array $ids, array $payload)
	{
		return $this->model->whereIn('id', $ids)->update($payload);
	}
}
