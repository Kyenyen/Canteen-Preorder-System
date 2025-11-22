<?php

// app/Http/Controllers/CanteenController.php
namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CanteenController extends Controller
{
    public function getMenu()
    {
        return response()->json(Menu::where('is_available', true)->get());
    }

    // Store a new order with Payment Info
    public function storeOrder(Request $request)
    {
        $request->validate([
            'pickup_time' => 'required|string',
            'payment_method' => 'required|string|in:ewallet,duitnow,card', // Validate payment method
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $menuItem = Menu::find($item['id']);
                $lineTotal = $menuItem->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $orderItemsData[] = [
                    'menu_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                ];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'pickup_time' => $request->pickup_time,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'paid', // In a real app, verify this via gateway callback
                'payment_method' => $request->payment_method
            ]);

            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            DB::commit();

            return response()->json(['message' => 'Order placed!', 'order_id' => $order->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order failed'], 500);
        }
    }

    // Get Order History for the logged-in Student
    public function getHistory()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items.menu') 
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    // Student cancels their own order
    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Cannot cancel an order that is being prepared or is ready.'], 400);
        }

        $order->update(['status' => 'cancelled']);
        // Note: You might need refund logic here if payment_status was 'paid'
        
        return response()->json(['message' => 'Order cancelled successfully.']);
    }

    // Admin: View All Orders
    public function getAdminOrders()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['items.menu', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    // Admin: Update Status
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['status' => 'required|in:pending,ready,completed,cancelled']);
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Order status updated']);
    }
}