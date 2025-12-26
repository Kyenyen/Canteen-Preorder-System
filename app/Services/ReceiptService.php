<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ReceiptService
{
    /** Generate Receipt HTML */
    public function generateReceiptHtml(Order $order): string
    {
        return view('canteen.receipts.receipt', ['order' => $order])->render();
    }
}
