<?php

namespace App\Repositories\Interfaces;

interface BulkActionRepositoryInterface
{
	public function bulkDelete(array $ids);
	public function bulkUpdate(array $ids, array $payload);
}
