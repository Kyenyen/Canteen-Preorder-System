<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Notifications\CancellationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // 1. Get Order History (Student)
    public function index()
    {
        $userId = Auth::id();
        Log::info('Fetching order history', ['user_id' => $userId]);
        
        $orders = Order::where('user_id', $userId)
            ->with(['products', 'payment']) // Load products and payment status
            ->orderBy('date', 'desc')
            ->orderBy('pickup_time', 'desc')
            ->get();
        
        Log::info('Orders retrieved', ['count' => $orders->count(), 'orders' => $orders]);
        
        return response()->json($orders);
    }

    // 3. Cancel Order (Student)
    public function cancel($id)
    {
        // Find order belonging to the logged-in user
        $order = Order::where('order_id', $id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        // Only allow cancellation if status is Preparing
        if ($order->status !== 'Preparing') {
            return response()->json(['message' => 'Cannot cancel order that is already ready or completed.'], 400);
        }

        $order->update(['status' => 'Cancelled']);
        
        return response()->json(['message' => 'Order cancelled successfully']);
    }

    // 4. View All Orders (Admin)
    public function indexAdmin()
    {
        // Optional: Double check role if not relying solely on route middleware
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['products', 'user', 'payment'])
            ->orderBy('date', 'desc')
            ->orderBy('pickup_time', 'asc') // Admins usually want to see earliest pickup first
            ->get();

        return response()->json($orders);
    }

    // 5. Update Status (Admin)
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:Pending,Preparing,Ready,Completed,Cancelled'
        ]);

        // Find by custom ID
        $order = Order::where('order_id', $id)->firstOrFail();
        
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Order status updated to ' . $request->status]);
    }

    // 6. Send Cancellation Email
    public function sendCancellationEmail($id)
    {
        // Find order belonging to the logged-in user
        $order = Order::where('order_id', $id)
                      ->where('user_id', Auth::id())
                      ->with(['products', 'user'])
                      ->firstOrFail();

        // Verify the order is cancelled
        if ($order->status !== 'Cancelled') {
            return response()->json(['message' => 'Order is not cancelled.'], 400);
        }

        try {
            // Send cancellation email notification
            $order->user->notify(new CancellationMail($order));
            
            Log::info('Cancellation email sent', ['order_id' => $order->order_id]);
            
            return response()->json(['message' => 'Cancellation email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['message' => 'Failed to send cancellation email'], 500);
        }
    }

    // 7. Get Single Order (Student)
    public function show($id)
    {
        // Find order belonging to the logged-in user
        $order = Order::where('order_id', $id)
                      ->where('user_id', Auth::id())
                      ->with(['products', 'payment'])
                      ->firstOrFail();

        return response()->json($order);
    }
}