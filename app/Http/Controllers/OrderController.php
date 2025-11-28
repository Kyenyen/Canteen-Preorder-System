<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 1. Create Order (Student)
    public function store(Request $request)
    {
        $request->validate([
            'pickup_time' => 'required|string',
            'dining_option' => 'required|in:dine-in,takeaway',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,product_id', 
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate Custom Order ID (O0001, O0002...)
            $lastOrder = Order::orderBy('order_id', 'desc')->first();
            $number = $lastOrder ? intval(substr($lastOrder->order_id, 1)) + 1 : 1;
            $newOrderId = 'O' . str_pad($number, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $syncData = [];

            // Calculate Totals & Prepare Pivot Data
            foreach ($request->items as $item) {
                $product = Product::where('product_id', $item['product_id'])->firstOrFail();
                
                $quantity = $item['quantity'];
                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;

                $syncData[$product->product_id] = [
                    'quantity' => $quantity,
                    'subtotal' => $subtotal 
                ];
            }

            // Create Order
            $order = Order::create([
                'order_id' => $newOrderId,
                'user_id' => Auth::id(), 
                'pickup_time' => $request->pickup_time,
                'dining_option' => $request->dining_option,
                'total' => $totalAmount,
                'status' => 'Pending',
                'date' => now()->toDateString(), 
            ]);

            // Fill 'orderlist' pivot table
            $order->products()->attach($syncData);

            DB::commit();

            return response()->json([
                'message' => 'Order created', 
                'order_id' => $order->order_id,
                'total' => $totalAmount
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order failed', 'details' => $e->getMessage()], 500);
        }
    }

    // 2. Get Order History (Student)
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['products', 'payment']) // Load products and payment status
            ->orderBy('date', 'desc')
            ->orderBy('pickup_time', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    // 3. Cancel Order (Student)
    public function cancel($id)
    {
        // Find order belonging to the logged-in user
        $order = Order::where('order_id', $id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        // Only allow cancellation if status is Pending
        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'Cannot cancel order that is already preparing or completed.'], 400);
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
}