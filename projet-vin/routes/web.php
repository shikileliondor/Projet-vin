<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStatusController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    Route::patch('products/{product}/status', [ProductStatusController::class, 'update'])->name('products.status.update');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

    Route::get('stock', StockController::class)->name('stock.index');
    Route::get('stock/entries/create', [StockEntryController::class, 'create'])->name('stock.entries.create');
    Route::post('stock/entries', [StockEntryController::class, 'store'])->name('stock.entries.store');
    Route::get('stock/exits/create', [StockExitController::class, 'create'])->name('stock.exits.create');
    Route::post('stock/exits', [StockExitController::class, 'store'])->name('stock.exits.store');
    Route::get('stock/movements', [StockMovementController::class, 'index'])->name('stock.movements.index');

    Route::resource('inventories', InventoryController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('users', UserController::class)->only(['index', 'store', 'update']);

    Route::get('settings/application', [SettingController::class, 'edit'])->name('settings.application.edit');
    Route::put('settings/application', [SettingController::class, 'update'])->name('settings.application.update');
});
