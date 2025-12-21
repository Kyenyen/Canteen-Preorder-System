<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class SalesReportController extends Controller
{
    /**
     * Get comprehensive sales report data
     */
    public function getSalesReport()
    {
        // Get total revenue (only paid orders, exclude cancelled)
        $totalRevenue = Order::whereHas('payment', function ($query) {
            $query->where('refunded', false);
        })
        ->where('status', '!=', 'Cancelled')
        ->sum('total');

        // Get total orders (all orders)
        $totalOrders = Order::count();

        // Get average order value
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get daily revenue for the last 30 days
        $dailyRevenue = Order::whereHas('payment', function ($query) {
            $query->where('refunded', false);
        })
            ->where('status', '!=', 'Cancelled')
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->select('date', DB::raw('SUM(total) as revenue'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'revenue' => (float) $item->revenue,
                ];
            });

        // Get daily orders for the last 30 days
        $dailyOrders = Order::where('date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->select('date', DB::raw('COUNT(*) as order_count'))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'order_count' => (int) $item->order_count,
                ];
            });

        // Revenue by category (for bar chart)
        $revenueByCategory = Product::join('orderlist', 'products.product_id', '=', 'orderlist.product_id')
            ->join('orders', 'orderlist.order_id', '=', 'orders.order_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.refunded', false)
            ->groupBy('products.category_id')
            ->select(
                'products.category_id',
                DB::raw('SUM(orderlist.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.order_id) as order_count')
            )
            ->with('category:category_id,name')
            ->get();

        // Get category names for the revenue data
        $revenueByCategory = Product::select('category_id')
            ->distinct()
            ->with(['category' => function ($query) {
                $query->select('category_id', 'name');
            }])
            ->get()
            ->map(function ($product) {
                $categoryName = $product->category ? $product->category->name : 'Unknown';
                $revenue = Product::where('category_id', $product->category_id)
                    ->join('orderlist', 'products.product_id', '=', 'orderlist.product_id')
                    ->join('orders', 'orderlist.order_id', '=', 'orders.order_id')
                    ->join('payments', 'orders.order_id', '=', 'payments.order_id')
                    ->where('payments.refunded', false)
                    ->where('orders.status', '!=', 'Cancelled')
                    ->sum('orderlist.subtotal');

                return [
                    'category' => $categoryName,
                    'revenue' => (float) $revenue,
                ];
            })
            ->filter(function ($item) {
                return $item['revenue'] > 0;
            })
            ->sortByDesc('revenue')
            ->values();

        // Top selling items
        $topSellingItems = Product::select(
            'products.product_id',
            'products.name',
            'products.price',
            'products.photo',
            'products.category_id',
            DB::raw('SUM(orderlist.quantity) as total_quantity'),
            DB::raw('SUM(orderlist.subtotal) as total_revenue')
        )
            ->join('orderlist', 'products.product_id', '=', 'orderlist.product_id')
            ->join('orders', 'orderlist.order_id', '=', 'orders.order_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.refunded', false)
            ->where('orders.status', '!=', 'Cancelled')
            ->with('category:category_id,name')
            ->groupBy('products.product_id', 'products.name', 'products.price', 'products.photo', 'products.category_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'photo' => $item->photo,
                    'category' => $item->category ? $item->category->name : 'Unknown',
                    'total_quantity' => (int) $item->total_quantity,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            });

        // Order statistics
        $orderStats = [
            'total_orders' => $totalOrders,
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
        ];

        // Revenue statistics
        $revenueStats = [
            'total_revenue' => (float) $totalRevenue,
            'average_order_value' => (float) $averageOrderValue,
            'today_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', now()->toDateString())
                ->sum('total'),
            'this_week_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', '>=', now()->startOfWeek()->toDateString())
                ->sum('total'),
            'this_month_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', '>=', now()->startOfMonth()->toDateString())
                ->sum('total'),
        ];

        return response()->json([
            'revenue_stats' => $revenueStats,
            'order_stats' => $orderStats,
            'daily_revenue' => $dailyRevenue,
            'daily_orders' => $dailyOrders,
            'revenue_by_category' => $revenueByCategory,
            'top_selling_items' => $topSellingItems,
        ]);
    }

    /**
     * Generate PDF for sales report
     */
    public function generatePDF()
    {
        // Get total revenue (only paid orders, exclude cancelled)
        $totalRevenue = Order::whereHas('payment', function ($query) {
            $query->where('refunded', false);
        })
        ->where('status', '!=', 'Cancelled')
        ->sum('total');

        // Get total orders
        $totalOrders = Order::count();

        // Get average order value
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue by category
        $revenueByCategory = Product::select('category_id')
            ->distinct()
            ->with(['category' => function ($query) {
                $query->select('category_id', 'name');
            }])
            ->get()
            ->map(function ($product) {
                $categoryName = $product->category ? $product->category->name : 'Unknown';
                $revenue = Product::where('category_id', $product->category_id)
                    ->join('orderlist', 'products.product_id', '=', 'orderlist.product_id')
                    ->join('orders', 'orderlist.order_id', '=', 'orders.order_id')
                    ->join('payments', 'orders.order_id', '=', 'payments.order_id')
                    ->where('payments.refunded', false)
                    ->where('orders.status', '!=', 'Cancelled')
                    ->sum('orderlist.subtotal');

                return [
                    'category' => $categoryName,
                    'revenue' => (float) $revenue,
                ];
            })
            ->filter(function ($item) {
                return $item['revenue'] > 0;
            })
            ->sortByDesc('revenue')
            ->values();

        // Top selling items
        $topSellingItems = Product::select(
            'products.product_id',
            'products.name',
            'products.price',
            DB::raw('SUM(orderlist.quantity) as total_quantity'),
            DB::raw('SUM(orderlist.subtotal) as total_revenue')
        )
            ->join('orderlist', 'products.product_id', '=', 'orderlist.product_id')
            ->join('orders', 'orderlist.order_id', '=', 'orders.order_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.refunded', false)
            ->where('orders.status', '!=', 'Cancelled')
            ->with('category:category_id,name')
            ->groupBy('products.product_id', 'products.name', 'products.price')
            ->orderBy('total_quantity', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'category' => $item->category ? $item->category->name : 'Unknown',
                    'total_quantity' => (int) $item->total_quantity,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            });

        // Order statistics
        $orderStats = [
            'total_orders' => $totalOrders,
            'completed_orders' => Order::where('status', 'Completed')->count(),
            'pending_orders' => Order::whereIn('status', ['Preparing', 'Ready'])->count(),
            'cancelled_orders' => Order::where('status', 'Cancelled')->count(),
        ];

        // Revenue statistics
        $revenueStats = [
            'total_revenue' => (float) $totalRevenue,
            'average_order_value' => (float) $averageOrderValue,
            'today_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', now()->toDateString())
                ->sum('total'),
            'this_week_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', '>=', now()->startOfWeek()->toDateString())
                ->sum('total'),
            'this_month_revenue' => (float) Order::whereHas('payment', function ($query) {
                $query->where('refunded', false);
            })
                ->where('status', '!=', 'Cancelled')
                ->where('date', '>=', now()->startOfMonth()->toDateString())
                ->sum('total'),
        ];

        $data = [
            'revenueStats' => $revenueStats,
            'orderStats' => $orderStats,
            'revenueByCategory' => $revenueByCategory,
            'topSellingItems' => $topSellingItems,
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        // Generate PDF using Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('pdf.sales-report', $data)->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
