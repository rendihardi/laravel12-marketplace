<?php

use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreBalanceController;
use App\Http\Controllers\StoreBalanceHistoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('user', UserController::class);
Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);

Route::apiResource('store', StoreController::class);
Route::get('/store/all/paginated', [StoreController::class, 'getAllPaginated']);
Route::put('/store/{id}/verified', [StoreController::class, 'updateVerifiedStatus']);

Route::apiResource('store-balance', StoreBalanceController::class)->except('store', 'update', 'destroy');
Route::get('/store-balance/all/paginated', [StoreBalanceController::class, 'getAllPaginated']);

Route::apiResource('store-balance-history', StoreBalanceHistoryController::class)->except('store', 'update', 'destroy');
Route::get('/store-balance-history/all/paginated', [StoreBalanceHistoryController::class, 'getAllPaginated']);

Route::apiResource('withdrawal', WithdrawalController::class);
Route::get('/withdrawal/all/paginated', [WithdrawalController::class, 'getAllPaginated']);
Route::put('/withdrawal/{id}/approve', [WithdrawalController::class, 'approve']);

Route::apiResource('buyer', BuyerController::class);
Route::get('/buyer/all/paginated', [BuyerController::class, 'getAllPaginated']);

Route::apiResource('product-category', ProductCategoryController::class);
Route::get('/product-category/all/paginated', [ProductCategoryController::class, 'getAllPaginated']);
Route::get('/product-category/slug/{slug}', [ProductCategoryController::class, 'getBySlug']);

Route::apiResource('product', ProductController::class);
Route::get('/product/all/paginated', [ProductController::class, 'getAllPaginated']);
Route::get('/product/slug/{slug}', [ProductController::class, 'getBySlug']);
