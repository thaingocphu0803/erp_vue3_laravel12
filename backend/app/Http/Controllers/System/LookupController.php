<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\LookupByModelRequest;
use App\Http\Resources\Lookup\DepartmentLookupResource;
use App\Http\Resources\Lookup\LookupResource;
use App\Services\System\LookupService;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
	use HasResponse;

	public function __construct(
		protected LookupService $lookupService
	) {}

	public function list(LookupByModelRequest $lookupByModelRequest)
	{

		$model = $lookupByModelRequest->validated('model');

		$resourceMap = [
			'departments' => DepartmentLookupResource::class,
			'positions' => LookupResource::class,
			'roles' => LookupResource::class
		];

		$list = $this->lookupService->list($model);

		if (!$list) {
			$message = 'common-list.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return $resourceMap[$model]::collection($list)->response();
	}
}
