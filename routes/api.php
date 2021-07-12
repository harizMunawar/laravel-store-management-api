<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;

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

    Route::get('/stores/', [StoreController::class, 'index'])
        ->name('list-store');
    Route::get('/stores/{id}/', [StoreController::class, 'show'])
        ->name('detail-store');
    Route::post('/stores/', [StoreController::class, 'create'])
        ->name('create-store');
    Route::put('/stores/{id}/', [StoreController::class, 'update'])
        ->name('update-store');
    Route::delete('/stores/{id}/', [StoreController::class, 'destroy'])
        ->name('delete-store');
    Route::post('/stores/{id}/owner/users/{user_id}/', [StoreController::class, 'assignOwner'])
        ->name('assign-store-owner');
    Route::delete('/stores/{id}/owner/', [StoreController::class, 'unassignOwner'])
        ->name('unassign-store-owner');
});