<?php

namespace App\Http\Controllers\Hr;

use App\Enum\Status;
use App\Enum\Table;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\Department\StoreDepartmentRequest;
use App\Models\Department;
use App\Trait\HasAutoCode;
use App\Trait\HasResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class DepartmentController extends Controller
{
	use HasResponse, HasAutoCode;

	public function __construct(){}

	public function create(StoreDepartmentRequest $request){
		$payload = $request->only('name', 'code', 'parent_id', 'description');
		$payload['status'] = Status::ACTIVE->value;
		$payload['created_by'] = Auth::id();

		if(is_null($payload['code'])){
			$payload['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEP');
		}

		try{
			return DB::transaction(function () use ($payload) {
				Department::create($payload);

				$message = 'department.alert.success.create';

				return $this->jsonResponse($message, JsonResponse::HTTP_OK);
			});
		}catch(\Exception $e){
			$message = 'department.alert.error.create';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
