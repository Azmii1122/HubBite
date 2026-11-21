<?php

use App\Http\Controllers\Seller\AnalyticsController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\HelpController;
use App\Http\Controllers\Seller\MenuController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\SettingsController;
use App\Http\Controllers\Seller\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:merchant'])->prefix('seller')->name('seller.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('menus', MenuController::class)->except(['show']);
    Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::patch('/store/status', [StoreController::class, 'updateStatus'])->name('store.updateStatus');
    Route::patch('/store/crowd', [StoreController::class, 'updateCrowd'])->name('store.updateCrowd');
    Route::patch('/store/go-live', [StoreController::class, 'goLive'])->name('store.goLive');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/kitchen', [AnalyticsController::class, 'kitchen'])->name('analytics.kitchen');
    Route::get('/analytics/optimization', [AnalyticsController::class, 'optimization'])->name('analytics.optimization');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/help', [HelpController::class, 'index'])->name('help');
});
