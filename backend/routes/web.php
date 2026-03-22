<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('admin/menu', \App\Http\Controllers\AdminWeb\MenuController::class)->names('admin.menu');
    Route::resource('admin/coupon', \App\Http\Controllers\AdminWeb\CouponController::class)->names('admin.coupon');
    Route::resource('admin/order', \App\Http\Controllers\AdminWeb\OrderController::class)->names('admin.order')->only(['index', 'show']);
    Route::put('admin/order/{order}/status', [\App\Http\Controllers\AdminWeb\OrderController::class, 'updateStatus'])->name('admin.order.status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
