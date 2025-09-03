<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes();

// Redirect root to login if not authenticated
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('/debts', [OrderController::class, 'debts'])->name('orders.debts');
    
    // Receipts
    Route::resource('receipts', ReceiptController::class)->except(['edit', 'update']);
    
    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::get('/debts-on-us', [PurchaseController::class, 'debts'])->name('purchases.debts');
    
    // Attachments
    Route::post('/orders/{order}/attachments', [OrderController::class, 'uploadAttachment'])->name('orders.attachments');
    Route::delete('/attachments/{attachment}', [OrderController::class, 'deleteAttachment'])->name('attachments.destroy');
    Route::delete('/audio/{audio}', [OrderController::class, 'deleteAudio'])->name('audio.destroy');
});
