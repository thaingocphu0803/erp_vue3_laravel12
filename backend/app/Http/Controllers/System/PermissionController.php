<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\PermissionCollection;
use App\Models\Permission;
use App\Trait\HasResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
	use HasResponse;

    public function index(Request $request){
		try{
			$permissions = Permission::whereNot('slug', 'admin')->get();
			return new PermissionCollection($permissions);
		}catch(\Exception $e){
			$message = 'common-list.alert.error.getPermissions';
			return $this->exceptionResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
