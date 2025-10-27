<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'email-service',
        'timestamp' => now()->toISOString(),
    ]);
});

// Send order confirmation email
Route::post('/order-confirmation', [EmailController::class, 'sendOrderConfirmation']);
