<?php

namespace App\Http\Controllers\Organization;

use App\Enum\Status;
use App\Enum\Table;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Department\IndexDepartmentRequest;
use App\Http\Requests\Organization\Department\StoreDepartmentRequest;
use App\Models\Department;
use App\Trait\HasAutoCode;
use App\Trait\HasResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Organization\DepartmentResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class DepartmentController extends Controller
{
	use HasResponse, HasAutoCode;

	public function __construct() {}

	public function create(StoreDepartmentRequest $request)
	{
		$payload = $request->only('name', 'code', 'parent_id', 'description');
		$payload['status'] = Status::ACTIVE->value;
		$payload['created_by'] = Auth::id();

		if (is_null($payload['code'])) {
			$payload['code'] = $this->generateCode(Table::DEPARTMENT->value, 'DEP');
		}

		try {
			return DB::transaction(function () use ($payload) {
				Department::create($payload);

				$message = 'department.alert.success.create';

				return $this->jsonResponse($message, JsonResponse::HTTP_OK);
			});
		} catch (\Exception $e) {
			$message = 'department.alert.error.create';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	public function index(IndexDepartmentRequest $request)
	{
		$filters = $request->only('status');
		$sort =  $request->only(['sortKey', 'sortOrder']);
		$search = $request->input('search');
		$itemPerPage = (int)$request->input('itemsPerPage');

		try {

			$departments = Department::filter($filters)
			->search($search)
			->sortOrder($sort)
			->paginate($itemPerPage);

			return DepartmentResource::collection($departments)->response();

	} catch (\Exception $e) {
		return $this->exceptionResponse('common-list.alert.error.getTableData', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	}
	}
}
