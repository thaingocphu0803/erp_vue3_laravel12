<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\PermissionCollection;
use App\Models\Permission;
use App\Trait\FormatResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
	use FormatResponse;

	public function index(Request $request)
	{
		try {
			$permissions = Permission::whereNot('slug', 'admin')->get();
			return new PermissionCollection($permissions);
		} catch (\Exception $e) {
			$message = 'common-list.alert.error.getPermissions';
			return $this->exceptionResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
