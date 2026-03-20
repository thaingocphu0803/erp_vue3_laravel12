<?php

namespace App\Http\Controllers\Organization;

use App\Enum\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Position\StorePositionRequest;
use App\Models\Position;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
	use HasResponse;

	public function create(StorePositionRequest $request)
	{
		$payload = $request->only('name', 'department_id', 'description');
		$payload['created_by'] = Auth::id();

		$syncPayload = $request->input('permissions');

		try {
			return DB::transaction(function () use ($payload, $syncPayload) {
				$position = Position::create($payload);
				$position->permissions()->attach($syncPayload);

				$message = 'position.alert.success.create';

				return $this->jsonResponse($message, JsonResponse::HTTP_OK);
			});
		} catch (\Exception $e) {
			$message = 'position.alert.error.create';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
