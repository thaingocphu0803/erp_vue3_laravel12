<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\ListByProvinceRequest;
use App\Http\Resources\System\AdministrativeUnitResource;
use App\Services\System\AdministrativeUnitService;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdministrativeUnitController extends Controller
{
	use HasResponse;

	public function __construct(
		protected AdministrativeUnitService $administrativeUnitService
	) {}

	public function provinces(Request $request)
	{
		$provinces = $this->administrativeUnitService->provinces();

		if ($provinces === false) {
			$message = 'common-list.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return AdministrativeUnitResource::collection($provinces)->response();
	}

	public function wards(ListByProvinceRequest $listByProvinceRequest)
	{
		$provinceCode = $listByProvinceRequest->validated()['province_code'];

		$wards = $this->administrativeUnitService->wards($provinceCode);

		if ($wards === false) {
			$message = 'common-list.alert.error.getList';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		return AdministrativeUnitResource::collection($wards)->response();
	}
}
