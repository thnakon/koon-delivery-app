<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'google']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

Route::get('/categories', [App\Http\Controllers\MenuController::class, 'categories']);
Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index']);
Route::get('/menu/{menuItem}', [App\Http\Controllers\MenuController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', App\Http\Controllers\OrderController::class)->only(['index', 'show']);
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->middleware('idempotency');
    
    Route::post('/coupons/validate', [App\Http\Controllers\CouponController::class, 'validateCoupon']);
    Route::post('/payments/charge', [App\Http\Controllers\PaymentController::class, 'charge'])->middleware('idempotency');
});

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // Note: Admin role middleware should be added here
    Route::apiResource('menu', App\Http\Controllers\Admin\MenuController::class);
    Route::apiResource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    Route::put('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
});
