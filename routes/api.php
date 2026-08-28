<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\StoreBalanceController;
use App\Http\Controllers\StoreBalanceHistoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Shipping proxy routes (public — used by frontend for address search & cost calculation)
Route::get('/shipping/destination', [ShippingController::class, 'destination']);
Route::get('/shipping/province', [ShippingController::class, 'province']);
Route::get('/shipping/city/{provinceId}', [ShippingController::class, 'city']);
Route::get('/shipping/district/{cityId}', [ShippingController::class, 'district']);
Route::get('/shipping/sub-district/{districtId}', [ShippingController::class, 'subDistrict']);
Route::post('/shipping/domestic-cost', [ShippingController::class, 'domesticCost']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/my-store', [StoreController::class, 'getByUser']);
    Route::get('/my-store-balance', [StoreBalanceController::class, 'myStoreBalance']);

    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);
    Route::apiResource('user', UserController::class);

    Route::put('/store/{id}/verified', [StoreController::class, 'updateVerifiedStatus']);
    Route::apiResource('store', StoreController::class)->except(['index', 'show']);

    Route::get('/store-balance/all/paginated', [StoreBalanceController::class, 'getAllPaginated']);
    Route::apiResource('store-balance', StoreBalanceController::class)->except('store', 'update', 'destroy');

    Route::get('/store-balance-history/all/paginated', [StoreBalanceHistoryController::class, 'getAllPaginated']);
    Route::apiResource('store-balance-history', StoreBalanceHistoryController::class)->except('store', 'update', 'destroy');

    Route::get('/withdrawal/all/paginated', [WithdrawalController::class, 'getAllPaginated']);
    Route::put('/withdrawal/{id}/approve', [WithdrawalController::class, 'approve']);
    Route::apiResource('withdrawal', WithdrawalController::class);

    Route::get('/buyer/all/paginated', [BuyerController::class, 'getAllPaginated']);
    Route::apiResource('buyer', BuyerController::class);

    Route::get('/transaction/all/paginated', [TransactionController::class, 'getAllPaginated']);
    Route::get('/transaction/code/{code}', [TransactionController::class, 'getByCode']);
    Route::apiResource('transaction', TransactionController::class);

    // Product write routes (create, update, delete) — requires auth
    Route::apiResource('product', ProductController::class)->except(['index', 'show']);

    // Product Category write routes (create, update, delete) — requires auth
    Route::apiResource('product-category', ProductCategoryController::class)->except(['index', 'show']);

});

Route::get('/product-category', [ProductCategoryController::class, 'index']);
Route::get('/product-category/all/paginated', [ProductCategoryController::class, 'getAllPaginated']);
Route::get('/product-category/slug/{slug}', [ProductCategoryController::class, 'getBySlug']);
Route::get('/product-category/{id}', [ProductCategoryController::class, 'show']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/all/paginated', [ProductController::class, 'getAllPaginated']);
Route::get('/product/slug/{slug}', [ProductController::class, 'getBySlug']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::apiResource('product-review', ProductReviewController::class);

Route::get('/store', [StoreController::class, 'index']);
Route::get('/store/all/paginated', [StoreController::class, 'getAllPaginated']);
Route::get('/store/username/{username}', [StoreController::class, 'getByUsername']);
Route::get('/store/{id}', [StoreController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/midtrans-callback', [MidtransController::class, 'callback']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me'])->name('me');
