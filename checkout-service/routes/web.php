<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// API Routes for testing
Route::prefix('api')->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => 'checkout-service',
            'timestamp' => now()->toISOString(),
        ]);
    });

    // Get all orders
    Route::get('/orders', function () {
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'user_email' => 'user@example.com',
                    'total' => 79.98,
                    'status' => 'completed',
                    'items' => [
                        ['product_id' => 1, 'quantity' => 2, 'price' => 29.99],
                        ['product_id' => 2, 'quantity' => 1, 'price' => 49.99]
                    ]
                ]
            ]
        ]);
    });

    // Get single order
    Route::get('/orders/{id}', function ($id) {
        return response()->json([
            'data' => [
                'id' => $id,
                'user_email' => 'user@example.com',
                'total' => 79.98,
                'status' => 'completed',
                'items' => [
                    ['product_id' => 1, 'quantity' => 2, 'price' => 29.99]
                ]
            ]
        ]);
    });

    // Create new order (POST)
    Route::post('/orders', function (Request $request) {
        return response()->json([
            'message' => 'Order created successfully',
            'data' => [
                'id' => rand(100, 999),
                'user_email' => $request->input('email', 'user@example.com'),
                'total' => $request->input('total', 0),
                'status' => 'pending'
            ]
        ], 201);
    });
});
