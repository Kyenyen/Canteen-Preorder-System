<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'method' => 'required|in:ewallet,card,cash',
        ]);

        $order = Order::where('order_id', $request->order_id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        // Check if already paid
        if ($order->payment) {
             return response()->json(['message' => 'Order already paid'], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Generate Custom Payment ID (PAY0001, etc.)
            $lastPayment = Payment::orderBy('payment_id', 'desc')->first();
            $number = $lastPayment ? intval(substr($lastPayment->payment_id, 3)) + 1 : 1;
            $newPaymentId = 'PAY' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // 2. Create Payment Record
            Payment::create([
                'payment_id' => $newPaymentId,
                'order_id' => $order->order_id,
                'method' => $request->method,
                'refunded' => 0,      // Default value
                'refund_date' => null // Default value
            ]);

            // 3. Update Order Status
            $order->update(['status' => 'Paid']); // Or 'Preparing'

            DB::commit();

            return response()->json(['message' => 'Payment successful']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Payment failed', 'details' => $e->getMessage()], 500);
        }
    }
}