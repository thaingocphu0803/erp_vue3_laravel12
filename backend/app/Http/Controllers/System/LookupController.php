<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\LookupResource;
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

	public function list(Request $request)
	{
		$allowed = [
			'departments',
			'positions'
		];

		$modelName = $request->input('model');

		if (!in_array($modelName, $allowed)) {
			$message = 'common-list.alert.error.badRequest';
			return $this->exceptionResponse($message, JsonResponse::HTTP_BAD_REQUEST);
		}

		$list = $this->lookupService->list($modelName);

		if (!$list) {
			$message = 'common-list.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return LookupResource::collection($list)->response();
	}
}
