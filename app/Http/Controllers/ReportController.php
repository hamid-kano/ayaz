<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // حساب الإحصائيات الحقيقية
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $pendingOrders = Order::whereIn('status', ['new', 'in-progress'])->count();
        
        // حساب الإيرادات (مجموع المدفوعات)
        $totalRevenue = Receipt::sum('amount');
        
        // حساب المصروفات
        $totalExpenses = Purchase::sum('amount');
        
        // صافي الربح
        $netProfit = $totalRevenue - $totalExpenses;
        
        // الديون المستحقة (الطلبات غير المدفوعة بالكامل)
        $outstandingDebtsSyp = Order::selectRaw('SUM(cost - COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)) as debt')
            ->where('currency', 'syp')
            ->whereRaw('cost > COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)')
            ->value('debt') ?? 0;
            
        $outstandingDebtsUsd = Order::selectRaw('SUM(cost - COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)) as debt')
            ->where('currency', 'usd')
            ->whereRaw('cost > COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)')
            ->value('debt') ?? 0;
        
        // الديون علينا (المشتريات غير المدفوعة)
        $debtsOnUsSyp = Purchase::where('status', 'debt')->where('currency', 'syp')->sum('amount');
        $debtsOnUsUsd = Purchase::where('status', 'debt')->where('currency', 'usd')->sum('amount');
        
        $stats = [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'outstanding_debts_syp' => $outstandingDebtsSyp,
            'outstanding_debts_usd' => $outstandingDebtsUsd,
            'debts_on_us_syp' => $debtsOnUsSyp,
            'debts_on_us_usd' => $debtsOnUsUsd
        ];

        // البيانات الشهرية للرسم البياني
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('ar')->translatedFormat('F');
            
            $monthlyRevenue = Receipt::whereYear('receipt_date', $date->year)
                ->whereMonth('receipt_date', $date->month)
                ->sum('amount');
                
            $monthlyExpenses = Purchase::whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->sum('amount');
                
            $monthlyData[] = [
                'month' => $monthName,
                'revenue' => $monthlyRevenue,
                'expenses' => $monthlyExpenses
            ];
        }

        // أفضل العملاء
        $topCustomers = Order::select('customer_name')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(cost) as total_amount')
            ->groupBy('customer_name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'name' => $customer->customer_name,
                    'orders' => $customer->orders_count,
                    'total' => $customer->total_amount
                ];
            })
            ->toArray();

        return view('reports.index', compact('stats', 'monthlyData', 'topCustomers'));
    }
}