<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ReceiptService
{
    /**
     * Generate HTML receipt for an order
     */
    public function generateReceiptHtml(Order $order): string
    {
        $receiptDate = now()->format('Y-m-d H:i:s');

        // Calculate formatted totals outside the heredoc
        $totalFormatted = number_format($order->total, 2);

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f5f5f5;
                    padding: 20px;
                }
                .receipt {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: white;
                    padding: 40px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #f97316;
                    padding-bottom: 20px;
                }
                .header h1 {
                    font-size: 28px;
                    color: #1f2937;
                    margin-bottom: 5px;
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
                    padding: 15px;
                    background-color: #f9fafb;
                    border-radius: 6px;
                }
                .info-group h3 {
                    font-size: 12px;
                    color: #6b7280;
                    font-weight: 600;
                    text-transform: uppercase;
                    margin-bottom: 8px;
                }
                .info-group p {
                    font-size: 14px;
                    color: #1f2937;
                    line-height: 1.6;
                }
                .items-section {
                    margin-bottom: 30px;
                }
                .items-section h2 {
                    font-size: 16px;
                    color: #1f2937;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #e5e7eb;
                    padding-bottom: 10px;
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
                    color: #1f2937;
                    font-weight: 500;
                }
                .item-qty {
                    text-align: center;
                    color: #6b7280;
                }
                .item-price {
                    text-align: right;
                    color: #1f2937;
                    font-weight: 500;
                }
                .totals {
                    margin: 30px 0;
                    padding: 20px;
                    background-color: #f9fafb;
                    border-radius: 6px;
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
                    font-size: 18px;
                    font-weight: 700;
                    color: #f97316;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #e5e7eb;
                    color: #6b7280;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <div class="header">
                    <h1>🍔 UniCanteen Receipt</h1>
                    <p>Order Confirmation & Details</p>
                </div>

                <div class="order-info">
                    <div class="info-group">
                        <h3>Order Number</h3>
                        <p>{$order->order_id}</p>
                    </div>
                    <div class="info-group">
                        <h3>Customer Name</h3>
                        <p>{$order->user->username}</p>
                    </div>
                    <div class="info-group">
                        <h3>Email</h3>
                        <p>{$order->user->email}</p>
                    </div>
                    <div class="info-group">
                        <h3>Order Date</h3>
                        <p>{$order->date}</p>
                    </div>
                    <div class="info-group">
                        <h3>Pickup Time</h3>
                        <p>{$order->pickup_time}</p>
                    </div>
                </div>

                <div class="items-section">
                    <h2>Order Items</h2>
HTML;

        // Add items
        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $subtotal = $product->pivot->subtotal;
            $subtotalFormatted = number_format($subtotal, 2);

            $html .= <<<HTML
            <div class="item">
                <div class="item-name">{$product->name}</div>
                <div class="item-qty">{$quantity}x</div>
                <div class="item-price">RM {$subtotalFormatted}</div>
            </div>
HTML;
        }

        $html .= <<<HTML
                </div>

                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>RM {$totalFormatted}</span>
                    </div>
                    <div class="total-row final">
                        <span>Total Amount:</span>
                        <span>RM {$totalFormatted}</span>
                    </div>
                </div>

                <div class="footer">
                    <p>Receipt Generated: {$receiptDate}</p>
                    <p>Thank you for your order!</p>
                </div>
            </div>
        </body>
        </html>
HTML;

        return $html;
    }
}
