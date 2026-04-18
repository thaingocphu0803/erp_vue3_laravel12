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
		$permissions = Permission::whereNot('slug', 'admin')->get();

		if ($permissions->isEmpty()) {
			abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'System configurations missing: Permissions must be seeded before usage.');
		}

		return new PermissionCollection($permissions);
	}
}
