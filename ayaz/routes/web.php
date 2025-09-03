<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PurchaseController;

// Authentication Routes
Auth::routes();

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('/debts', [OrderController::class, 'debts'])->name('orders.debts');
    
    // Receipts
    Route::resource('receipts', ReceiptController::class)->except(['edit', 'update']);
    
    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::get('/debts-on-us', [PurchaseController::class, 'debts'])->name('purchases.debts');
    
    // API Routes for AJAX
    Route::prefix('api')->group(function () {
        Route::get('/orders/search', [OrderController::class, 'search'])->name('api.orders.search');
        Route::post('/orders/{order}/attachments', [OrderController::class, 'uploadAttachment'])->name('api.orders.attachments');
        Route::delete('/attachments/{attachment}', [OrderController::class, 'deleteAttachment'])->name('api.attachments.delete');
    });
});
