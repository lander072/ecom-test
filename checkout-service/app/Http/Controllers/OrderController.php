<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Catalog service URL
     */
    private function getCatalogServiceUrl(): string
    {
        return env('CATALOG_SERVICE_URL', 'http://localhost:8001');
    }

    /**
     * Email service URL
     */
    private function getEmailServiceUrl(): string
    {
        return env('EMAIL_SERVICE_URL', 'http://localhost:8003');
    }

    /**
     * Send order confirmation email
     */
    private function sendOrderConfirmationEmail(Order $order): void
    {
        try {
            $url = "{$this->getEmailServiceUrl()}/api/order-confirmation";

            // Prepare email data
            $emailData = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email,
                'customer_name' => $order->customer_name ?? 'Valued Customer',
                'order_total' => $order->total_amount,
                'order_items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->product_price,
                    ];
                })->toArray(),
                'shipping_address' => $order->shipping_address 
                    ? (is_array($order->shipping_address) 
                        ? implode(', ', array_filter($order->shipping_address)) 
                        : $order->shipping_address)
                    : null,
            ];

            \Log::info("Sending order confirmation email", [
                'email_service_url' => $url,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email,
            ]);

            $response = Http::timeout(10)->post($url, $emailData);

            if ($response->successful()) {
                \Log::info("Order confirmation email sent successfully", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'email_response' => $response->json(),
                ]);
            } else {
                \Log::warning("Failed to send order confirmation email", [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            // Don't fail the order if email fails - just log it
            \Log::error("Exception while sending order confirmation email", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Validate and fetch product from Catalog Service
     */
    private function validateAndFetchProduct(int $productId): ?array
    {
        try {
            $url = "{$this->getCatalogServiceUrl()}/api/products/{$productId}";

            // Log catalog service URL and product ID
            \Log::info("Attempting to fetch product from catalog service", [
                'catalog_url' => $this->getCatalogServiceUrl(),
                'full_url' => $url,
                'product_id' => $productId,
                'timestamp' => now()->toIso8601String()
            ]);
            
            \Log::info("Fetching product from catalog", ['url' => $url, 'product_id' => $productId]);
            
            $response = Http::timeout(20)->get($url);
            
            \Log::info("Catalog service response", [
                'status' => $response->status(),
                'body' => $response->body(),
                'product_id' => $productId
            ]);

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error("Failed to fetch product {$productId} from catalog service", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate all order items against Catalog Service
     */
    private function validateOrderItems(array $items): array
    {
        $validatedItems = [];
        $errors = [];

        foreach ($items as $index => $item) {
            $product = $this->validateAndFetchProduct($item['product_id']);

            if (!$product) {
                $errors[] = "Product with ID {$item['product_id']} not found or unavailable";
                continue;
            }

            // Check if product is active
            if (!$product['is_active']) {
                $errors[] = "{$product['name']} is no longer available";
                continue;
            }

            // Check stock availability
            if ($product['stock'] < $item['quantity']) {
                $errors[] = "{$product['name']} has insufficient stock. Available: {$product['stock']}, Requested: {$item['quantity']}";
                continue;
            }

            // Use real data from Catalog Service
            $validatedItems[] = [
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product_description' => $product['description'],
                'product_price' => (float) $product['price'], // Use real price
                'product_image_url' => $product['image_url'],
                'quantity' => $item['quantity'],
                'available_stock' => $product['stock'],
            ];
        }

        return [
            'validated_items' => $validatedItems,
            'errors' => $errors
        ];
    }

    /**
     * Create a new order (Checkout).
     */
    public function store(Request $request): JsonResponse
    {
        // Validate request - Now only requires product_id and quantity
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_email' => 'required|email',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal,stripe,bank_transfer,cash_on_delivery',
            'shipping_address' => 'nullable|array',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate items against Catalog Service
            $validation = $this->validateOrderItems($request->items);

            if (!empty($validation['errors'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            $validatedItems = $validation['validated_items'];

            if (empty($validatedItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items to process',
                ], 422);
            }

            DB::beginTransaction();

            // Calculate totals using validated prices from Catalog
            $subtotal = 0;
            foreach ($validatedItems as $item) {
                $subtotal += $item['product_price'] * $item['quantity'];
            }

            $tax = $subtotal * 0.10; // 10% tax
            $shippingCost = 0; // Free shipping
            $totalAmount = $subtotal + $tax + $shippingCost;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $request->user_id ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'customer_email' => $request->customer_email,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'notes' => $request->notes,
            ]);

            // Create order items using validated data
            foreach ($validatedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_description' => $item['product_description'],
                    'product_price' => $item['product_price'],
                    'product_image_url' => $item['product_image_url'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['product_price'] * $item['quantity'],
                ]);
            }

            // Create transaction
            $transaction = Transaction::create([
                'order_id' => $order->id,
                'transaction_id' => Transaction::generateTransactionId(),
                'amount' => $totalAmount,
                'currency' => 'USD',
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_gateway' => 'simulated', // For demo purposes
            ]);

            // Simulate payment processing
            // In production, this would integrate with Stripe, PayPal, etc.
            $paymentSuccess = $this->simulatePaymentProcessing($transaction);

            if ($paymentSuccess) {
                // Update transaction status
                $transaction->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'payment_gateway_response' => [
                        'success' => true,
                        'message' => 'Payment processed successfully',
                        'timestamp' => now()->toIso8601String(),
                    ],
                ]);

                // Update order status
                $order->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now(),
                ]);
            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => 'Payment declined by processor',
                ]);

                $order->update([
                    'payment_status' => 'failed',
                ]);

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed',
                ], 402);
            }

            DB::commit();

            // Load relationships for response
            $order->load(['orderItems', 'transactions']);

            // Send order confirmation email (non-blocking - don't fail order if email fails)
            $this->sendOrderConfirmationEmail($order);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders (with optional filters).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['orderItems', 'transactions']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by customer email
        if ($request->has('customer_email')) {
            $query->where('customer_email', $request->customer_email);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $orders = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Get a specific order by ID or order number.
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        // Check if identifier is order number or ID
        if (str_starts_with($identifier, 'ORD-')) {
            $order = Order::with(['orderItems', 'transactions'])
                ->where('order_number', $identifier)
                ->first();
        } else {
            $order = Order::with(['orderItems', 'transactions'])
                ->find($identifier);
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
            ],
        ]);
    }

    /**
     * Simulate payment processing (for demo purposes).
     * In production, integrate with actual payment gateway.
     */
    private function simulatePaymentProcessing(Transaction $transaction): bool
    {
        // Simulate 2-second processing delay
        sleep(2);

        // Simulate 95% success rate
        return rand(1, 100) <= 95;
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already cancelled',
            ], 400);
        }

        if (in_array($order->status, ['shipped', 'delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order that has been shipped or delivered',
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'order' => $order,
            ],
        ]);
    }

    /**
     * Get order statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
