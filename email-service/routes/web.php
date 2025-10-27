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
            'service' => 'email-service',
            'timestamp' => now()->toISOString(),
        ]);
    });

    // Get email logs
    Route::get('/emails', function () {
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'to' => 'user@example.com',
                    'subject' => 'Order Confirmation #1',
                    'status' => 'sent',
                    'sent_at' => now()->subHours(2)->toISOString()
                ],
                [
                    'id' => 2,
                    'to' => 'another@example.com',
                    'subject' => 'Order Confirmation #2',
                    'status' => 'sent',
                    'sent_at' => now()->subHour()->toISOString()
                ]
            ]
        ]);
    });

    // Send email (POST)
    Route::post('/send', function (Request $request) {
        return response()->json([
            'message' => 'Email queued successfully',
            'data' => [
                'id' => rand(100, 999),
                'to' => $request->input('email', 'user@example.com'),
                'subject' => $request->input('subject', 'Order Confirmation'),
                'status' => 'queued',
                'queued_at' => now()->toISOString()
            ]
        ], 201);
    });

    // Get email status
    Route::get('/emails/{id}', function ($id) {
        return response()->json([
            'data' => [
                'id' => $id,
                'to' => 'user@example.com',
                'subject' => 'Order Confirmation #' . $id,
                'status' => 'sent',
                'sent_at' => now()->toISOString()
            ]
        ]);
    });
});
