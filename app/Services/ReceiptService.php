<?php

namespace App\Services;

use App\Models\Order;

class ReceiptService
{
    /**
     * Generate HTML receipt for an order using Blade
     */
    public function generateReceiptHtml(Order $order): string
    {
        // We simply render the view and return it as a string
        return view('canteen.emails.receipt', [
            'order' => $order
        ])->render();
    }
}