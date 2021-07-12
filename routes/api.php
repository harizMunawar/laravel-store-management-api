<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;

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
    Route::post('/users/', [UserController::class, 'create'])
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
    Route::post('/categories/', [CategoryController::class, 'create'])
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

    Route::get('/products/', [ProductController::class, 'index'])
        ->name('list-product');
    Route::get('/products/store/{store_id}/', [ProductController::class, 'storeProduct'])
        ->name('list-product-by-store');
    Route::get('/products/{id}/', [ProductController::class, 'show'])
        ->name('detail-product');
    Route::post('/stores/{store_id}/products/', [ProductController::class, 'create'])
        ->name('create-product');
    Route::put('/products/{id}/', [ProductController::class, 'update'])
        ->name('update-product');
    Route::delete('/products/{id}/', [ProductController::class, 'destroy'])
        ->name('delete-product');
    Route::post('products/{id}/categories/{category_id}/', [ProductController::class, 'addCategory'])
        ->name('add-category');
    Route::delete('products/{id}/categories/{category_id}/', [ProductController::class, 'removeCategory'])
        ->name('remove-category');
    Route::put('products/{id}/stock/{new_stock}/', [ProductController::class, 'updateStock'])
        ->name('update-stock');
});