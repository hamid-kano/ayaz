<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Receipt;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => 45,
            'completed_orders' => 32,
            'pending_orders' => 13,
            'total_revenue' => 125000,
            'total_expenses' => 45000,
            'net_profit' => 80000,
            'outstanding_debts' => 25000,
            'debts_on_us' => 15000
        ];

        $monthlyData = [
            ['month' => 'يناير', 'revenue' => 15000, 'expenses' => 8000],
            ['month' => 'فبراير', 'revenue' => 18000, 'expenses' => 9500],
            ['month' => 'مارس', 'revenue' => 22000, 'expenses' => 11000],
            ['month' => 'أبريل', 'revenue' => 19000, 'expenses' => 8500],
            ['month' => 'مايو', 'revenue' => 25000, 'expenses' => 12000],
            ['month' => 'يونيو', 'revenue' => 26000, 'expenses' => 13500]
        ];

        $topCustomers = [
            ['name' => 'أحمد محمد', 'orders' => 8, 'total' => 15000],
            ['name' => 'سارة أحمد', 'orders' => 6, 'total' => 12000],
            ['name' => 'محمد علي', 'orders' => 5, 'total' => 9500],
            ['name' => 'فاطمة حسن', 'orders' => 4, 'total' => 8000]
        ];

        return view('reports.index', compact('stats', 'monthlyData', 'topCustomers'));
    }
}