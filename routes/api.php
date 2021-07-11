<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/login/', [AuthController::class, 'login'])
        ->withoutMiddleware(['auth:sanctum'])
        ->name('login');
    Route::post('/logout/', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/users/', [UserController::class, 'index'])
        ->withoutMiddleware(['auth:sanctum'])
        ->name('list-user');
    Route::get('/users/{id}/', [UserController::class, 'show'])
        ->withoutMiddleware(['auth:sanctum'])
        ->name('detail-user');
    Route::post('/users/', [UserController::class, 'store'])
        ->name('create-user');
    Route::put('/users/{id}/', [UserController::class, 'update'])
        ->name('update-user');
    Route::delete('/users/{id}/', [UserController::class, 'destroy'])
        ->name('delete-user');
    
    Route::get('/categories/', [CategoryController::class, 'index'])
        ->withoutMiddleware(['auth:sanctum'])
        ->name('list-category');
    Route::get('/categories/{id}/', [CategoryController::class, 'show'])
        ->withoutMiddleware(['auth:sanctum'])
        ->name('detail-category');
    Route::post('/categories/', [CategoryController::class, 'store'])
        ->name('create-category');
    Route::put('/categories/{id}/', [CategoryController::class, 'update'])
        ->name('update-category');
    Route::delete('/categories/{id}/', [CategoryController::class, 'destroy'])
        ->name('delete-category');
});