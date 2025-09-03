<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Receipt;
use App\Models\Purchase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'new_orders' => Order::where('status', 'new')->count(),
            'in_progress_orders' => Order::where('status', 'in-progress')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'total_receipts_usd' => Receipt::where('currency', 'usd')->sum('amount'),
            'total_receipts_syp' => Receipt::where('currency', 'syp')->sum('amount'),
            'total_purchases_usd' => Purchase::where('currency', 'usd')->sum('amount'),
            'total_purchases_syp' => Purchase::where('currency', 'syp')->sum('amount'),
            'debt_purchases_usd' => Purchase::where('status', 'debt')->where('currency', 'usd')->sum('amount'),
            'debt_purchases_syp' => Purchase::where('status', 'debt')->where('currency', 'syp')->sum('amount'),
        ];

        // Recent activities
        $recent_orders = Order::latest()->take(5)->get();
        $recent_receipts = Receipt::with('order')->latest()->take(5)->get();
        $recent_purchases = Purchase::latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_orders', 'recent_receipts', 'recent_purchases'));
    }
}