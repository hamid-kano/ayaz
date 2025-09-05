<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    
    // Users (Admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // Update player ID for notifications
    Route::post('/user/update-player-id', [UserController::class, 'updatePlayerId'])->name('user.update-player-id');
    
    // Attachments
    Route::post('/orders/{order}/attachments', [OrderController::class, 'uploadAttachment'])->name('orders.attachments');
    Route::delete('/attachments/{attachment}', [OrderController::class, 'deleteAttachment'])->name('attachments.destroy');
    
    // Audio
    Route::post('/orders/{order}/audio', [OrderController::class, 'uploadAudio'])->name('orders.audio');
    Route::delete('/audio/{audio}', [OrderController::class, 'deleteAudio'])->name('audio.destroy');
    
    // Audio file serving
    Route::get('/audio/{filename}', function($filename) {
        $path = public_path('audio/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    })->name('audio.serve');
});
