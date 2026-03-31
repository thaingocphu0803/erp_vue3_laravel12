<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommonRequest;
use App\Http\Requests\Organization\Position\StorePositionRequest;
use App\Http\Resources\Organization\PositionResource;
use App\Services\Organization\PositionService;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
	use HasResponse;

	public function __construct(
		protected PositionService $positionService
	){}

	public function create(StorePositionRequest $request)
	{
		$positionPayload = $request->validated();

		if($this->positionService->create($positionPayload)){
			$message = 'position.alert.success.create';
			return $this->jsonResponse($message, JsonResponse::HTTP_OK);
		}

		$message = 'position.alert.error.create';
		return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function index(IndexCommonRequest $indexCommonRequest){
		$positionPayload = $indexCommonRequest->validated();
		$positions = $this->positionService->paginate($positionPayload);

		if (!$positions) {
			$message = 'common-list.alert.error.getTableData';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return PositionResource::collection($positions)->response();
	}
}
