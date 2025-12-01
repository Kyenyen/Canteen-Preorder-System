<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = [];

        // 1. Get Active Order (If Student)
        // Checks for any order that isn't 'Completed' or 'Cancelled'
        if ($user->role === 'student') {
            $activeOrder = Order::where('user_id', $user->user_id) 
                ->whereIn('status', ['Pending', 'Preparing', 'Ready'])
                // Assuming your Order model uses 'date' or timestamps. 
                // Adjust 'created_at' if your table uses that instead.
                ->orderBy('date', 'desc') 
                ->first();
            
            if ($activeOrder) {
                $data['activeOrder'] = $activeOrder;
            }
        }

        // 2. Dynamic Promo: Pick 1 random product to be the "Special"
        $promoProduct = Product::where('is_available', 1)->inRandomOrder()->first();

        if ($promoProduct) {
            $data['promo'] = [
                'title' => $promoProduct->name . ' Special',
                'description' => "Today's highlight! Enjoy our delicious " . $promoProduct->name . " for only RM " . number_format($promoProduct->price, 2) . ". Order now before it runs out!",
            ];
        } else {
            // Fallback if no products exist yet
            $data['promo'] = [
                'title' => 'UniCanteen Special',
                'description' => "Check out our menu for delicious meals at student-friendly prices!",
            ];
        }

        // 3. Popular Items: Pick 3 random items (Excluding the promo item to avoid duplicates)
        $query = Product::where('is_available', 1);

        if ($promoProduct) {
            $query->where('product_id', '!=', $promoProduct->product_id);
        }

        $data['popularItems'] = $query->inRandomOrder()->limit(3)->get();

        return response()->json($data);
    }
}