Thank You for Your Order!

Hi {{ $orderData['customer_name'] }},

We're excited to confirm that we've received your order and it's being processed!

Order Details:
--------------
Order Number: {{ $orderData['order_number'] }}
Order Date: {{ $orderData['order_date'] }}
Total Amount: ${{ number_format($orderData['order_total'], 2) }}

Items Ordered:
--------------
@foreach($orderData['order_items'] as $item)
- {{ $item['product_name'] }}
  Quantity: {{ $item['quantity'] }}
  Price: ${{ number_format($item['price'], 2) }}
  Subtotal: ${{ number_format($item['quantity'] * $item['price'], 2) }}

@endforeach
--------------
Total: ${{ number_format($orderData['order_total'], 2) }}

@if(!empty($orderData['shipping_address']))
Shipping Address:
{{ $orderData['shipping_address'] }}
@endif

We'll send you another email when your order ships.

Thank you for shopping with us!

---
If you have any questions about your order, please contact our support team.
© {{ date('Y') }} E-Commerce Platform. All rights reserved.
