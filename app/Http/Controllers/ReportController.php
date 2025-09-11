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
        $orders = Order::with(['items', 'receipts'])->where('order_date', '>=', $startDate)->get();
        $outstandingDebtsSyp = 0;
        $outstandingDebtsUsd = 0;
        
        foreach ($orders as $order) {
            $totalSyp = $order->items->where('currency', 'syp')->sum(function($item) {
                return $item->quantity * $item->price;
            });
            $totalUsd = $order->items->where('currency', 'usd')->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            $paidSyp = $order->receipts->where('currency', 'syp')->sum('amount');
            $paidUsd = $order->receipts->where('currency', 'usd')->sum('amount');
            
            $remainingSyp = $totalSyp - $paidSyp;
            $remainingUsd = $totalUsd - $paidUsd;
            
            if ($remainingSyp > 0) $outstandingDebtsSyp += $remainingSyp;
            if ($remainingUsd > 0) $outstandingDebtsUsd += $remainingUsd;
        }
        
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
        $topCustomers = Order::with('items')
            ->where('order_date', '>=', $startDate)
            ->get()
            ->groupBy('customer_name')
            ->map(function ($orders, $customerName) {
                $totalSyp = $orders->sum(function($order) {
                    return $order->items->where('currency', 'syp')->sum(function($item) {
                        return $item->quantity * $item->price;
                    });
                });
                $totalUsd = $orders->sum(function($order) {
                    return $order->items->where('currency', 'usd')->sum(function($item) {
                        return $item->quantity * $item->price;
                    });
                });
                return [
                    'name' => $customerName,
                    'orders' => $orders->count(),
                    'total_syp' => $totalSyp,
                    'total_usd' => $totalUsd
                ];
            })
            ->sortByDesc('orders')
            ->take(5)
            ->values()
            ->toArray();

        return view('reports.index', compact('stats', 'monthlyData', 'topCustomers'));
    }
}