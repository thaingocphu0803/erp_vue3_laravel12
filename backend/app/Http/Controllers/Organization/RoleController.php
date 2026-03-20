<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Role\StoreRoleRequest;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function create(StoreRoleRequest $request){
		dd($request->input());
	}
}
