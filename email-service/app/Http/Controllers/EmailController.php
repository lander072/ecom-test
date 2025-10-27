<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'order_number' => 'required|string',
            'customer_email' => 'required|email',
            'customer_name' => 'required|string',
            'order_total' => 'required|numeric',
            'order_items' => 'required|array',
            'order_items.*.product_name' => 'required|string',
            'order_items.*.quantity' => 'required|integer',
            'order_items.*.price' => 'required|numeric',
            'shipping_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare order data for email
            $orderData = [
                'customer_name' => $request->customer_name,
                'order_number' => $request->order_number,
                'order_total' => $request->order_total,
                'order_items' => $request->order_items,
                'shipping_address' => $request->shipping_address ?? 'N/A',
                'order_date' => now()->format('F j, Y'),
            ];

            // Create email record for tracking
            $email = Email::create([
                'recipient_email' => $request->customer_email,
                'recipient_name' => $request->customer_name,
                'subject' => 'Order Confirmation - ' . $request->order_number,
                'body_html' => 'Rendered via Laravel Mail',
                'body_text' => 'Rendered via Laravel Mail',
                'type' => 'order_confirmation',
                'reference_type' => 'order',
                'reference_id' => $request->order_id,
                'status' => 'sending',
                'metadata' => [
                    'order_number' => $request->order_number,
                    'order_total' => $request->order_total,
                    'items_count' => count($request->order_items),
                ],
            ]);

            // Send email using Laravel Mail with Gmail SMTP
            Mail::to($request->customer_email, $request->customer_name)
                ->send(new OrderConfirmation($orderData));

            // Mark as sent
            $email->markAsSent();
            $email->update([
                'email_provider_response' => [
                    'provider' => 'gmail_smtp',
                    'timestamp' => now()->toISOString(),
                    'recipient' => $request->customer_email,
                ]
            ]);

            Log::info("Order confirmation email sent successfully", [
                'email_id' => $email->id,
                'order_id' => $request->order_id,
                'order_number' => $request->order_number,
                'recipient' => $request->customer_email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order confirmation email sent successfully',
                'data' => [
                    'email_id' => $email->id,
                    'status' => $email->status,
                    'recipient' => $email->recipient_email,
                    'order_number' => $request->order_number,
                ]
            ], 201);

        } catch (\Exception $e) {
            // Mark email as failed if it was created
            if (isset($email)) {
                $email->markAsFailed($e->getMessage());
            }

            Log::error('Order confirmation email failed: ' . $e->getMessage(), [
                'order_id' => $request->order_id ?? null,
                'order_number' => $request->order_number ?? null,
                'recipient' => $request->customer_email ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send order confirmation email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
