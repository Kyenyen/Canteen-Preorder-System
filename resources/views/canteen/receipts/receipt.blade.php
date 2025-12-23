<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->order_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
        }
        .logo-icon {
            background-color: #fff7ed;
            color: #f97316;
            width: 64px;
            height: 64px;
            line-height: 64px;
            border-radius: 50%;
            display: inline-block;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .header p {
            color: #6b7280;
            font-size: 14px;
        }
        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #f3f4f6;
        }
        .info-group h3 {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .info-group p {
            font-size: 14px;
            color: #1f2937;
            line-height: 1.6;
            font-weight: 600;
        }
        .items-section {
            margin-bottom: 30px;
        }
        .items-section h2 {
            font-size: 16px;
            color: #111827;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            font-weight: 600;
        }
        .item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .item-name {
            color: #111827;
            font-weight: 600;
        }
        .item-qty {
            text-align: center;
            color: #9ca3af;
        }
        .item-price {
            text-align: right;
            color: #111827;
            font-weight: 600;
        }
        .totals {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        .total-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            padding: 8px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .total-row.final {
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 20px;
            font-weight: 800;
            color: #f97316;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 13px;
        }
        .footer p {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="https://raw.githubusercontent.com/Kyenyen/Canteen-Preorder-System/master/public/photos/logo.png" alt="KantinCanteen Logo" style="width: 94px; height: 64px; margin-bottom: 12px;">
            <h1>KantinCanteen Receipt</h1>
            <p>Order Confirmation & Details</p>
        </div>

        <div class="order-info">
            <div class="info-group">
                <h3>Order Number</h3>
                <p>#{{ $order->order_id }}</p>
            </div>
            <div class="info-group">
                <h3>Customer Name</h3>
                <p>{{ $order->user->username }}</p>
            </div>
            <div class="info-group">
                <h3>Email</h3>
                <p>{{ $order->user->email }}</p>
            </div>
            <div class="info-group">
                <h3>Order Date</h3>
                <p>{{ $order->date }}</p>
            </div>
            <div class="info-group">
                <h3>Pickup Time</h3>
                <p style="color: #f97316;">{{ $order->pickup_time }}</p>
            </div>
            <div class="info-group">
                <h3>Status</h3>
                <p style="color: #10b981;">{{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <div class="items-section">
            <h2>Order Items</h2>
            @foreach($order->products as $product)
            <div class="item">
                <div class="item-name">{{ $product->name }}</div>
                <div class="item-qty">{{ $product->pivot->quantity }}x</div>
                <div class="item-price">RM {{ number_format($product->pivot->subtotal, 2) }}</div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>RM {{ number_format($order->total, 2) }}</span>
            </div>
            <div class="total-row final">
                <span>Total Amount:</span>
                <span>RM {{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <p><strong>Receipt Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>&copy; {{ date('Y') }} KantinCanteen &bull; Skip the queue, eat better.</p>
            <p>Tunku Abdul Rahman University of Management and Technology</p>
        </div>
    </div>
</body>
</html>
