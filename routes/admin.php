<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'store', 'update', 'destroy']);
});
