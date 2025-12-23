<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        /* Base Styles */
        body { 
            background-color: #f4f7f9; 
            margin: 0; 
            padding: 0; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f9; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        
        /* Typography */
        h1 { color: #111827; font-size: 24px; font-weight: 700; margin: 0 0 16px 0; text-align: center; }
        p { color: #4b5563; font-size: 16px; line-height: 24px; margin: 0 0 20px 0; }
        
        /* Components */
        .logo-container { padding: 32px 0 20px 0; text-align: center; }
        .logo-icon { background-color: #fff7ed; color: #f97316; width: 64px; height: 64px; line-height: 64px; border-radius: 50%; display: inline-block; font-size: 28px; margin-bottom: 12px; }
        .content { padding: 0 40px 40px 40px; }
        
        /* Order Specific Styles */
        .order-meta { width: 100%; margin-bottom: 30px; background-color: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #f3f4f6; }
        .meta-label { color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .meta-value { color: #1f2937; font-size: 14px; font-weight: 600; margin-bottom: 12px; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .item-row td { padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #4b5563; }
        .total-section { border-top: 2px solid #f3f4f6; padding-top: 20px; }
        .total-row { font-size: 14px; color: #6b7280; margin-bottom: 8px; }
        .total-final { font-size: 20px; font-weight: 800; color: #f97316; margin-top: 10px; }
        
        /* Small Text */
        .security-note { font-size: 13px; color: #9ca3af; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 20px; margin-top: 20px; }
        .footer { text-align: center; padding: 24px; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    <table class="wrapper" role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="main" role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="logo-container">
                            <img src="{{ $message->embed(public_path('photos/logo.png')) }}" alt="KantinCanteen Logo" style="width: 94px; height: 64px; margin-bottom: 12px;">
                            <h1>KantinCanteen</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="content">
                            <h1 style="font-size: 22px; margin-bottom: 8px;">Order Confirmed!</h1>
                            <p style="text-align: center; color: #6b7280; font-size: 14px; margin-bottom: 30px;">Thank you for dining with KantinCanteen</p>
                            
                            <table class="order-meta" role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="50%" valign="top">
                                        <div class="meta-label">Order ID</div>
                                        <div class="meta-value">#{{ $order->order_id }}</div>
                                        <div class="meta-label">Customer</div>
                                        <div class="meta-value">{{ $order->user->username }}</div>
                                    </td>
                                    <td width="50%" valign="top">
                                        <div class="meta-label">Pickup Time</div>
                                        <div class="meta-value" style="color: #f97316;">{{ $order->pickup_time }}</div>
                                        <div class="meta-label">Order Date</div>
                                        <div class="meta-value">{{ $order->date }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table class="item-table" role="presentation" cellpadding="0" cellspacing="0" border="0">
                                @foreach($order->products as $product)
                                <tr class="item-row">
                                    <td align="left">
                                        <span style="font-weight: 600; color: #111827;">{{ $product->name }}</span>
                                    </td>
                                    <td align="center" width="60" style="color: #9ca3af;">{{ $product->pivot->quantity }}x</td>
                                    <td align="right" width="100" style="font-weight: 600; color: #111827;">RM {{ number_format($product->pivot->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </table>

                            <table width="100%" role="presentation" cellpadding="0" cellspacing="0" border="0" class="total-section">
                                <tr>
                                    <td align="left" class="total-row">Subtotal</td>
                                    <td align="right" class="total-row">RM {{ number_format($order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td align="left" class="total-final">Total Paid</td>
                                    <td align="right" class="total-final">RM {{ number_format($order->total, 2) }}</td>
                                </tr>
                            </table>
                            
                            <div class="security-note">
                                <p style="margin: 0;">Please show this email at the counter to collect your meal.</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    <p>
                        &copy; {{ date('Y') }} KantinCanteen &bull; Skip the queue, eat better.<br>
                        Tunku Abdul Rahman University of Management and Technology
                    </p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>