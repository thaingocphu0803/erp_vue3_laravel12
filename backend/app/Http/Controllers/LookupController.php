<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Trait\HandleResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
	use HandleResponse;

    public function list (Request $request){
		$allowed = [
			'department' => Department::class
		];

		$modelName = $request->input('model');

		if(!isset($allowed[$modelName])){
			dd('no');
		}

		$model = $allowed[$modelName];
		$list = $model::select('name', 'id')->get();

		$message = 'arlert.success.' . $modelName. 'List';
		$data['list'] = $list->toArray();

		return $this->jsonResponse($message, JsonResponse::HTTP_OK ,$data);
	}
}
