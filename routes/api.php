<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/login/', [AuthController::class, 'login'])->name('login');

Route::get('/users/', [UserController::class, 'index'])->name('list-user');
Route::get('/users/{id}/', [UserController::class, 'show'])->name('detail-user');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/users/', [UserController::class, 'store'])->name('create-user');
    Route::put('/users/{id}/', [UserController::class, 'update'])->name('update-user');
    Route::delete('/users/{id}/', [UserController::class, 'destroy'])->name('delete-user');
    Route::post('/logout/', [AuthController::class, 'logout']);
});