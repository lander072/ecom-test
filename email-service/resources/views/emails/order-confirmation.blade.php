<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0; text-align: center;">
                <table role="presentation" style="width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">Thank You for Your Order!</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #333;">
                                Hi {{ $orderData['customer_name'] }},
                            </p>
                            <p style="margin: 0 0 30px; font-size: 16px; line-height: 1.6; color: #333;">
                                We're excited to confirm that we've received your order and it's being processed!
                            </p>
                            
                            <!-- Order Details Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="margin: 0 0 15px; font-size: 20px; color: #667eea;">Order Details</h2>
                                        <p style="margin: 5px 0; font-size: 14px; color: #666;">
                                            <strong style="color: #333;">Order Number:</strong> {{ $orderData['order_number'] }}
                                        </p>
                                        <p style="margin: 5px 0; font-size: 14px; color: #666;">
                                            <strong style="color: #333;">Order Date:</strong> {{ $orderData['order_date'] }}
                                        </p>
                                        <p style="margin: 5px 0; font-size: 14px; color: #666;">
                                            <strong style="color: #333;">Total Amount:</strong> ${{ number_format($orderData['order_total'], 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Order Items -->
                            <h3 style="margin: 0 0 15px; font-size: 18px; color: #333;">Items Ordered:</h3>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #667eea;">
                                        <th style="text-align: left; padding: 12px 8px; font-size: 14px; color: #667eea;">Product</th>
                                        <th style="text-align: center; padding: 12px 8px; font-size: 14px; color: #667eea;">Qty</th>
                                        <th style="text-align: right; padding: 12px 8px; font-size: 14px; color: #667eea;">Price</th>
                                        <th style="text-align: right; padding: 12px 8px; font-size: 14px; color: #667eea;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderData['order_items'] as $item)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px 8px; font-size: 14px; color: #333;">{{ $item['product_name'] }}</td>
                                        <td style="text-align: center; padding: 12px 8px; font-size: 14px; color: #666;">{{ $item['quantity'] }}</td>
                                        <td style="text-align: right; padding: 12px 8px; font-size: 14px; color: #666;">${{ number_format($item['price'], 2) }}</td>
                                        <td style="text-align: right; padding: 12px 8px; font-size: 14px; color: #333; font-weight: 600;">${{ number_format($item['quantity'] * $item['price'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr style="border-top: 2px solid #667eea;">
                                        <td colspan="3" style="text-align: right; padding: 15px 8px; font-size: 16px; color: #333; font-weight: 600;">Total:</td>
                                        <td style="text-align: right; padding: 15px 8px; font-size: 16px; color: #667eea; font-weight: 700;">${{ number_format($orderData['order_total'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            @if(!empty($orderData['shipping_address']))
                            <!-- Shipping Address -->
                            <h3 style="margin: 0 0 10px; font-size: 18px; color: #333;">Shipping Address:</h3>
                            <p style="margin: 0 0 30px; font-size: 14px; line-height: 1.6; color: #666; background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                                {{ $orderData['shipping_address'] }}
                            </p>
                            @endif
                            
                            <p style="margin: 0 0 15px; font-size: 16px; line-height: 1.6; color: #333;">
                                We'll send you another email when your order ships.
                            </p>
                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #333;">
                                Thank you for shopping with us! 🎉
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px; background-color: #f8f9fa; border-top: 1px solid #eee; text-align: center;">
                            <p style="margin: 0 0 10px; font-size: 12px; color: #999;">
                                If you have any questions about your order, please contact our support team.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                © {{ date('Y') }} E-Commerce Platform. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
