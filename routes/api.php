<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DoctorApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/customers', [CustomerApiController::class, 'index']);
Route::get('/customers/{user}/', [CustomerApiController::class, 'show']);

Route::get('/doctors', [DoctorApiController::class, 'index']);
