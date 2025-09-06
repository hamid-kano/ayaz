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
    public function index(Request $request)
    {
        // تحديد الفترة الزمنية
        $period = $request->get('period', 180); // افتراضي 6 أشهر
        $startDate = Carbon::now()->subDays($period);
        
        // حساب الإحصائيات الحقيقية
        $totalOrders = Order::where('order_date', '>=', $startDate)->count();
        $completedOrders = Order::where('status', 'delivered')
            ->where('order_date', '>=', $startDate)->count();
        $pendingOrders = Order::whereIn('status', ['new', 'in-progress'])
            ->where('order_date', '>=', $startDate)->count();
        
        // حساب الإيرادات (مجموع المدفوعات)
        $totalRevenue = Receipt::where('receipt_date', '>=', $startDate)->sum('amount');
        
        // حساب المصروفات
        $totalExpenses = Purchase::where('purchase_date', '>=', $startDate)->sum('amount');
        
        // صافي الربح
        $netProfit = $totalRevenue - $totalExpenses;
        
        // الديون المستحقة (الطلبات غير المدفوعة بالكامل)
        $outstandingDebtsSyp = Order::selectRaw('SUM(cost - COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)) as debt')
            ->where('currency', 'syp')
            ->where('order_date', '>=', $startDate)
            ->whereRaw('cost > COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)')
            ->value('debt') ?? 0;
            
        $outstandingDebtsUsd = Order::selectRaw('SUM(cost - COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)) as debt')
            ->where('currency', 'usd')
            ->where('order_date', '>=', $startDate)
            ->whereRaw('cost > COALESCE((SELECT SUM(amount) FROM receipts WHERE receipts.order_id = orders.id AND receipts.currency = orders.currency), 0)')
            ->value('debt') ?? 0;
        
        // الديون علينا (المشتريات غير المدفوعة)
        $debtsOnUsSyp = Purchase::where('status', 'debt')
            ->where('currency', 'syp')
            ->where('purchase_date', '>=', $startDate)
            ->sum('amount');
        $debtsOnUsUsd = Purchase::where('status', 'debt')
            ->where('currency', 'usd')
            ->where('purchase_date', '>=', $startDate)
            ->sum('amount');
        
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
        $monthsCount = min(6, ceil($period / 30));
        $monthlyData = [];
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('ar')->translatedFormat('F');
            
            $monthlyRevenue = Receipt::whereYear('receipt_date', $date->year)
                ->whereMonth('receipt_date', $date->month)
                ->where('receipt_date', '>=', $startDate)
                ->sum('amount');
                
            $monthlyExpenses = Purchase::whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->where('purchase_date', '>=', $startDate)
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
            ->where('order_date', '>=', $startDate)
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