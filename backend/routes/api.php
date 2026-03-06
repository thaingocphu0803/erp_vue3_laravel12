<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hr\DepartmentController;
use App\Http\Controllers\LookupController;
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

// DepartmentController
Route::controller(LookupController::class)->prefix('lookup')->middleware('auth:sanctum')->group(function(){
	Route::get('list', 'list');
});
