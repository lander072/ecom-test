<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'checkout-service',
        'timestamp' => now()->toISOString(),
    ]);
});

// Order/Checkout endpoints
Route::prefix('orders')->group(function () {
    // Create new order (checkout)
    Route::post('/', [OrderController::class, 'store']);
    
    // Get all orders (with optional filters)
    Route::get('/', [OrderController::class, 'index']);
    
    // Get specific order by ID or order number
    Route::get('/{identifier}', [OrderController::class, 'show']);
    
    // Cancel an order
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    
    // Get order statistics
    Route::get('/stats/summary', [OrderController::class, 'statistics']);
});

// Alias for checkout endpoint
Route::post('/checkout', [OrderController::class, 'store']);

