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
        return view('canteen.receipts.receipt', ['order' => $order])->render();
    }
}
