<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin dashboard — landing setelah login. Quick stats untuk M2.
     */
    public function index(): View
    {
        $stats = [
            'orders_pending' => Order::where('status', 'pending')->count(),
            'orders_partial_paid' => Order::where('status', 'partial_paid')->count(),
            'orders_paid' => Order::where('status', 'paid')->count(),
            'orders_total' => Order::count(),
            'payments_to_verify' => OrderPayment::where('status', 'pending')->count(),
            'products_active' => Product::where('status', 'active')->count(),
        ];

        $recentOrders = Order::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
