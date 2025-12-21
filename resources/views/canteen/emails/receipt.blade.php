<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        /* Matches your Reset Password Styles Exactly */
        body { 
            background-color: #f4f7f9; 
            margin: 0; 
            padding: 0; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f9; padding: 40px 0; }
        /* Max-width 500px to match your reset email */
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 500px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        
        /* Typography */
        h1 { color: #111827; font-size: 24px; font-weight: 700; margin: 0 0 8px 0; text-align: center; }
        p { color: #4b5563; font-size: 16px; line-height: 24px; margin: 0; }
        
        /* Components */
        .logo-container { padding: 32px 0 20px 0; text-align: center; }
        .logo-icon { background-color: #fff7ed; color: #f97316; width: 64px; height: 64px; line-height: 64px; border-radius: 50%; display: inline-block; font-size: 28px; margin-bottom: 12px; }
        .content { padding: 0 40px 40px 40px; }

        /* Order Info Grid - Email Safe */
        .order-meta { width: 100%; margin-bottom: 25px; background-color: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #f3f4f6; }
        .meta-label { color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
        .meta-value { color: #1f2937; font-size: 14px; font-weight: 600; margin-bottom: 12px; }
        
        /* Items Table */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .item-row td { padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 15px; }
        
        /* Totals */
        .total-section { border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .total-final { font-size: 20px; font-weight: 800; color: #f97316; }

        .footer { text-align: center; padding: 24px; font-size: 13px; color: #9ca3af; line-height: 20px; }
    </style>
</head>
<body>
    <table class="wrapper" role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="main" role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="logo-container">
                            <div class="logo-icon">🍔</div>
                            <h1>Order Confirmed!</h1>
                            <p>Enjoy your meal, {{ $order->user->username }}!</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="content">
                            <table class="order-meta" role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="55%" valign="top">
                                        <div class="meta-label">Order ID</div>
                                        <div class="meta-value">#{{ $order->order_id }}</div>
                                        <div class="meta-label">Order Date</div>
                                        <div class="meta-value">{{ $order->date }}</div>
                                    </td>
                                    <td width="45%" valign="top" style="border-left: 1px solid #e5e7eb; padding-left: 20px;">
                                        <div class="meta-label">Pickup Time</div>
                                        <div class="meta-value" style="color: #f97316; font-size: 16px;">{{ $order->pickup_time }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table class="item-table" role="presentation" cellpadding="0" cellspacing="0" border="0">
                                @foreach($order->products as $product)
                                <tr class="item-row">
                                    <td align="left">
                                        <div style="font-weight: 600; color: #111827;">{{ $product->name }}</div>
                                        <div style="font-size: 12px; color: #9ca3af;">Qty: {{ $product->pivot->quantity }}</div>
                                    </td>
                                    <td align="right" valign="middle" style="font-weight: 600; color: #111827;">
                                        RM {{ number_format($product->pivot->subtotal, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            <table width="100%" role="presentation" cellpadding="0" cellspacing="0" border="0" class="total-section">
                                <tr>
                                    <td align="left" style="color: #4b5563; font-weight: 600;">Total Paid</td>
                                    <td align="right" class="total-final">RM {{ number_format($order->total, 2) }}</td>
                                </tr>
                            </table>

                            <p style="font-size: 13px; color: #9ca3af; text-align: center; margin-top: 30px; border-top: 1px solid #f3f4f6; padding-top: 20px;">
                                Please show this receipt at the <strong>UniCanteen</strong> counter to collect your order.
                            </p>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    <p>
                        &copy; {{ date('Y') }} UniCanteen &bull; Skip the queue, eat better.<br>
                        Tunku Abdul Rahman University of Management and Technology
                    </p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>