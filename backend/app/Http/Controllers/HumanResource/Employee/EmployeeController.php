<?php

namespace App\Http\Controllers\HumanResource\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResource\Employee\StoreEmployeeRequest;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
	public function create(StoreEmployeeRequest $storeEmployeeRequest)
	{
		dd($storeEmployeeRequest->validated());
	}
}
