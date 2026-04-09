<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HumanResource\Employee\EmployeeController;
use App\Http\Controllers\Organization\DepartmentController;
use App\Http\Controllers\Organization\PositionController;
use App\Http\Controllers\Organization\RoleController;
use App\Http\Controllers\System\AdministrativeUnitController;
use App\Http\Controllers\System\PermissionController;
use Illuminate\Support\Facades\Route;

// AuthController
Route::controller(AuthController::class)->prefix('auth')->group(function () {
	Route::post('login', 'login');
	Route::post('logout', 'logout')->middleware('auth:sanctum');
	Route::get('me', 'me')->middleware('auth:sanctum');
});

// DepartmentController
Route::controller(DepartmentController::class)->prefix('department')->middleware('auth:sanctum')->group(function () {
	Route::post('create', 'create');
	Route::get('index', 'index');
	Route::get('list', 'list');
});

// PermissionController
Route::controller(PermissionController::class)->prefix('permission')->middleware('auth:sanctum')->group(function () {
	Route::get('index', 'index');
});

// PositionController
Route::controller(PositionController::class)->prefix('position')->middleware('auth:sanctum')->group(function () {
	Route::post('create', 'create');
	Route::get('index', 'index');
	Route::get('list', 'list');
	Route::get('list-by-department', 'listByDepartment');
});

// RoleController
Route::controller(RoleController::class)->prefix('role')->middleware('auth:sanctum')->group(function () {
	Route::post('create', 'create');
	Route::get('index', 'index');
	Route::get('list', 'list');
});

// AdministrativeUnitController
Route::controller(AdministrativeUnitController::class)->prefix('administrative-units')->middleware('auth:sanctum')->group(function () {
	Route::get('provinces', 'provinces');
	Route::get('provinces/{provinceCode}/wards', 'wards');
});

// EmployeeController
Route::controller(EmployeeController::class)->prefix('employee')->middleware('auth:sanctum')->group(function () {
	Route::post('create', 'create');
});
