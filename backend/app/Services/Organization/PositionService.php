<?php

namespace App\Services\Organization;

use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionService
{
	/**
	 * Create a new class instance.
	 */
	public function __construct(
		protected PositionRepositoryInterface $positionRepositoryInterface
	) {}

	public function create(array $positionPayload)
	{
		$payload['created_by'] = Auth::id();

		try {
			return DB::transaction(function () use ($positionPayload) {
				$this->positionRepositoryInterface->create($positionPayload);
				return true;
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	public function paginate(array $paginationPayload){
		try{
			$positions = $this->positionRepositoryInterface->paginate($paginationPayload);
			return $positions;
		}catch(\Exception $e){
			return false;
		}
	}
}
