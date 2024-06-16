<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getall', [CategoryController::class, 'getall']);
Route::get('/get/{id}', [CategoryController::class, 'getById']);
Route::post('/create', [CategoryController::class, 'create']);
Route::put('/update/{id}', [CategoryController::class, 'update']);
Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
