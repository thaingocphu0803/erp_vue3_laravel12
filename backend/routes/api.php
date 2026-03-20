<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organization\DepartmentController;
use App\Http\Controllers\Organization\PositionController;
use App\Http\Controllers\Organization\RoleController;
use App\Http\Controllers\System\LookupController;
use App\Http\Controllers\System\PermissionController;
use Illuminate\Support\Facades\Route;

// AuthController
Route::controller(AuthController::class)->prefix('auth')->group(function(){
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('me', 'me')->middleware('auth:sanctum');
});

// DepartmentController
Route::controller(DepartmentController::class)->prefix('department')->middleware('auth:sanctum')->group(function(){
	Route::post('create', 'create');
	Route::get('index', 'index');
});

// LookupController
Route::controller(LookupController::class)->prefix('lookup')->middleware('auth:sanctum')->group(function(){
	Route::get('list', 'list');
});

// PermissionController
Route::controller(PermissionController::class)->prefix('permission')->middleware('auth:sanctum')->group(function(){
	Route::get('index', 'index');
});

// PositionController
Route::controller(PositionController::class)->prefix('position')->middleware('auth:sanctum')->group(function(){
	Route::post('create', 'create');
});

// RoleController
Route::controller(RoleController::class)->prefix('role')->middleware('auth:sanctum')->group(function(){
	Route::post('create', 'create');
});
