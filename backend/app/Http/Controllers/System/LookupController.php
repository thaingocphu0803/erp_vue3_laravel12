<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
	use HasResponse;

	public function list(Request $request)
	{
		$allowed = [
			'department' => Department::class
		];

		$modelName = $request->input('model');

		if (!isset($allowed[$modelName])) {
			$message = 'common-list.alert.error.badRequest';
			return $this->exceptionResponse($message, JsonResponse::HTTP_BAD_REQUEST);
		}

		try {
			$model = $allowed[$modelName];
			$list = $model::select('name', 'id')->get();

			$message = 'common-list.alert.success.getList';
			$data['list'] = $list->toArray();

			return $this->jsonResponse($message, JsonResponse::HTTP_OK, $data);
		} catch (\Exception $e) {
			$message = 'common-list.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
