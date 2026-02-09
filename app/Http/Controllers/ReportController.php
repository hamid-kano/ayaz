<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily()
    {
        $today = Carbon::today();
        $threeDaysAgo = Carbon::now()->subDays(3);
        
        // الطلبات الجديدة اليوم
        $newOrders = Order::whereDate('order_date', $today)
            ->with(['items'])
            ->get();
        
        // الطلبات الجديدة
        $newOrdersList = Order::where('status', 'new')->get();
        
        // الطلبات قيد التنفيذ
        $inProgressList = Order::where('status', 'in-progress')->get();
        
        // الطلبات المتأخرة
        $oldInProgress = Order::where('status', 'in-progress')
            ->where(function($query) use ($threeDaysAgo) {
                $query->where('delivery_date', '<=', $threeDaysAgo)
                      ->orWhere(function($q) use ($threeDaysAgo) {
                          $q->whereNull('delivery_date')
                            ->where('updated_at', '<=', $threeDaysAgo);
                      });
            })
            ->get();
        
        // إحصائيات الطلبات
        $ordersStats = [
            'new' => Order::where('status', 'new')->count(),
            'in_progress' => Order::where('status', 'in-progress')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'delivered' => Order::whereDate('updated_at', $today)->where('status', 'delivered')->count(),
        ];
        
        // المقبوضات اليوم
        $receiptsToday = Receipt::whereDate('receipt_date', $today)->get();
        $receiptsSyp = $receiptsToday->where('currency', 'syp')->sum('amount');
        $receiptsUsd = $receiptsToday->where('currency', 'usd')->sum('amount');
        
        // المشتريات اليوم
        $purchasesToday = Purchase::whereDate('purchase_date', $today)->get();
        $purchasesSyp = $purchasesToday->where('currency', 'syp')->sum('amount');
        $purchasesUsd = $purchasesToday->where('currency', 'usd')->sum('amount');
        
        // الديون لنا (استثناء الطلبات الملغاة)
        $orders = Order::with(['items', 'receipts'])
            ->whereNotIn('status', ['cancelled'])
            ->get();
        $debtsToUsSyp = 0;
        $debtsToUsUsd = 0;
        $debtsToUs = collect();
        
        foreach ($orders as $order) {
            $totalSyp = $order->items->where('currency', 'syp')->sum(function($item) {
                return $item->quantity * $item->price;
            });
            $totalUsd = $order->items->where('currency', 'usd')->sum(function($item) {
                return $item->quantity * $item->price;
            });
            $paidSyp = $order->receipts->where('currency', 'syp')->sum('amount');
            $paidUsd = $order->receipts->where('currency', 'usd')->sum('amount');
            
            $debtSyp = $totalSyp - $paidSyp;
            $debtUsd = $totalUsd - $paidUsd;
            
            if ($debtSyp > 0 || $debtUsd > 0) {
                $order->debt_syp = $debtSyp;
                $order->debt_usd = $debtUsd;
                $debtsToUs->push($order);
                if ($debtSyp > 0) $debtsToUsSyp += $debtSyp;
                if ($debtUsd > 0) $debtsToUsUsd += $debtUsd;
            }
        }
        
        // الديون علينا
        $debtsOnUs = Purchase::where('status', 'debt')->get();
        $debtsOnUsSyp = $debtsOnUs->where('currency', 'syp')->sum('amount');
        $debtsOnUsUsd = $debtsOnUs->where('currency', 'usd')->sum('amount');
        
        return view('reports.daily', compact(
            'today',
            'newOrders',
            'newOrdersList',
            'inProgressList',
            'oldInProgress',
            'ordersStats',
            'receiptsSyp',
            'receiptsUsd',
            'receiptsToday',
            'purchasesSyp',
            'purchasesUsd',
            'purchasesToday',
            'debtsToUsSyp',
            'debtsToUsUsd',
            'debtsToUs',
            'debtsOnUsSyp',
            'debtsOnUsUsd',
            'debtsOnUs'
        ));
    }

    public function sendTelegram()
    {
        try {
            Artisan::call('report:send-telegram');
            return redirect()->back()->with('success', 'تم إرسال التقرير إلى تلغرام بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل إرسال التقرير: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        // تحديد الفترة الزمنية
        $period = $request->get('period', 180); // افتراضي 6 أشهر
        $startDate = Carbon::now()->subDays($period);

        // حساب الإحصائيات الحقيقية
        $totalOrders = Order::where('order_date', '>=', $startDate)->count();
        $completedOrders = Order::where('status', 'delivered')
            ->where('order_date', '>=', $startDate)->count();
        $pendingOrders = Order::whereIn('status', ['new', 'in-progress', 'ready'])
            ->where('order_date', '>=', $startDate)->count();

        // حساب الإيرادات (مجموع المدفوعات)
        $totalRevenueSyp = Receipt::where('receipt_date', '>=', $startDate)
            ->where('currency', 'syp')->sum('amount');
        $totalRevenueUsd = Receipt::where('receipt_date', '>=', $startDate)
            ->where('currency', 'usd')->sum('amount');

        // حساب المصروفات
        $totalExpensesSyp = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'syp')->sum('amount');
        $totalExpensesUsd = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'usd')->sum('amount');

        // حساب المشتريات نقداً وبالدين
        $cashPurchasesSyp = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'syp')->where('status', 'cash')->sum('amount');
        $cashPurchasesUsd = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'usd')->where('status', 'cash')->sum('amount');
        $debtPurchasesSyp = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'syp')->where('status', 'debt')->sum('amount');
        $debtPurchasesUsd = Purchase::where('purchase_date', '>=', $startDate)
            ->where('currency', 'usd')->where('status', 'debt')->sum('amount');

        // حساب المبيعات نقداً وبالدين
        $orders = Order::with(['items', 'receipts'])->where('order_date', '>=', $startDate)->get();
        $totalSalesSyp = 0;
        $totalSalesUsd = 0;
        $cashSalesSyp = 0;
        $cashSalesUsd = 0;
        $debtSalesSyp = 0;
        $debtSalesUsd = 0;

        foreach ($orders as $order) {
            $orderTotalSyp = $order->items->where('currency', 'syp')->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            $orderTotalUsd = $order->items->where('currency', 'usd')->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $totalSalesSyp += $orderTotalSyp;
            $totalSalesUsd += $orderTotalUsd;

            $paidSyp = $order->receipts->where('currency', 'syp')->sum('amount');
            $paidUsd = $order->receipts->where('currency', 'usd')->sum('amount');

            $cashSalesSyp += $paidSyp;
            $cashSalesUsd += $paidUsd;

            $debtSalesSyp += ($orderTotalSyp - $paidSyp);
            $debtSalesUsd += ($orderTotalUsd - $paidUsd);
        }

        // صافي الربح
        $netProfitSyp = $totalRevenueSyp - $totalExpensesSyp;
        $netProfitUsd = $totalRevenueUsd - $totalExpensesUsd;

        // الديون المستحقة (الطلبات غير المدفوعة بالكامل)
        $outstandingDebtsSyp = 0;
        $outstandingDebtsUsd = 0;

        foreach ($orders as $order) {
            $totalSyp = $order->items->where('currency', 'syp')->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            $totalUsd = $order->items->where('currency', 'usd')->sum(function ($item) {
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
            'total_revenue_syp' => $totalRevenueSyp,
            'total_revenue_usd' => $totalRevenueUsd,
            'total_expenses_syp' => $totalExpensesSyp,
            'total_expenses_usd' => $totalExpensesUsd,
            'net_profit_syp' => $netProfitSyp,
            'net_profit_usd' => $netProfitUsd,
            'outstanding_debts_syp' => $outstandingDebtsSyp,
            'outstanding_debts_usd' => $outstandingDebtsUsd,
            'debts_on_us_syp' => $debtsOnUsSyp,
            'debts_on_us_usd' => $debtsOnUsUsd,
            'cash_purchases_syp' => $cashPurchasesSyp,
            'cash_purchases_usd' => $cashPurchasesUsd,
            'debt_purchases_syp' => $debtPurchasesSyp,
            'debt_purchases_usd' => $debtPurchasesUsd,
            'total_sales_syp' => $totalSalesSyp,
            'total_sales_usd' => $totalSalesUsd,
            'cash_sales_syp' => $cashSalesSyp,
            'cash_sales_usd' => $cashSalesUsd,
            'debt_sales_syp' => $debtSalesSyp,
            'debt_sales_usd' => $debtSalesUsd
        ];

        // البيانات الشهرية للرسم البياني
        $monthsCount = min(6, ceil($period / 30));
        $monthlyData = [];
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('ar')->translatedFormat('F');

            $monthlyRevenueSyp = Receipt::whereYear('receipt_date', $date->year)
                ->whereMonth('receipt_date', $date->month)
                ->where('receipt_date', '>=', $startDate)
                ->where('currency', 'syp')
                ->sum('amount');
            $monthlyRevenueUsd = Receipt::whereYear('receipt_date', $date->year)
                ->whereMonth('receipt_date', $date->month)
                ->where('receipt_date', '>=', $startDate)
                ->where('currency', 'usd')
                ->sum('amount');

            $monthlyExpensesSyp = Purchase::whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->where('purchase_date', '>=', $startDate)
                ->where('currency', 'syp')
                ->sum('amount');
            $monthlyExpensesUsd = Purchase::whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->where('purchase_date', '>=', $startDate)
                ->where('currency', 'usd')
                ->sum('amount');

            $monthlyData[] = [
                'month' => $monthName,
                'revenue_syp' => $monthlyRevenueSyp,
                'revenue_usd' => $monthlyRevenueUsd,
                'expenses_syp' => $monthlyExpensesSyp,
                'expenses_usd' => $monthlyExpensesUsd
            ];
        }

        // أفضل العملاء
        $topCustomers = Order::with('items')
            ->where('order_date', '>=', $startDate)
            ->get()
            ->groupBy('customer_name')
            ->map(function ($orders, $customerName) {
                $totalSyp = $orders->sum(function ($order) {
                    return $order->items->where('currency', 'syp')->sum(function ($item) {
                        return $item->quantity * $item->price;
                    });
                });
                $totalUsd = $orders->sum(function ($order) {
                    return $order->items->where('currency', 'usd')->sum(function ($item) {
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
