<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getall', [CategoryController::class, 'getall']);
Route::get('/getlist', [CategoryController::class, 'getlist']);
Route::get('/get/{id}', [CategoryController::class, 'getById']);
Route::post('/create', [CategoryController::class, 'create']);
Route::post('/update/{id}', [CategoryController::class, 'update']);
Route::delete('/delete/{id}', [CategoryController::class, 'delete']);

Route::get('/products', [ProductController::class, 'getAll']);
Route::post('/products', [ProductController::class, 'create']);
