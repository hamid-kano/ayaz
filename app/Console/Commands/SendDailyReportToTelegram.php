<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDailyReportToTelegram extends Command
{
    protected $signature = 'report:send-telegram';
    protected $description = 'إرسال التقرير اليومي إلى تلغرام';

    public function handle()
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');
            
            if (!$botToken || !$chatId) {
                $this->error('يجب تعيين TELEGRAM_BOT_TOKEN و TELEGRAM_CHAT_ID في ملف .env');
                Log::error('Telegram credentials missing in .env');
                return 1;
            }

            $today = Carbon::today();
            $threeDaysAgo = Carbon::now()->subDays(3);
        
            // جمع البيانات
            $newOrdersList = Order::where('status', 'new')->get();
            $inProgressList = Order::where('status', 'in-progress')->get();
            $oldInProgress = Order::where('status', 'in-progress')
                ->where(function($query) use ($threeDaysAgo) {
                    $query->where('delivery_date', '<=', $threeDaysAgo)
                          ->orWhere(function($q) use ($threeDaysAgo) {
                              $q->whereNull('delivery_date')
                                ->where('updated_at', '<=', $threeDaysAgo);
                          });
                })
                ->get();
        
            $newOrders = Order::whereDate('order_date', $today)->count();
            $ordersNew = $newOrdersList->count();
            $ordersInProgress = $inProgressList->count();
            $ordersReady = Order::where('status', 'ready')->count();
            $ordersDelivered = Order::whereDate('updated_at', $today)->where('status', 'delivered')->count();
        
            $receiptsToday = Receipt::whereDate('receipt_date', $today)->get();
            $receiptsSyp = $receiptsToday->where('currency', 'syp')->sum('amount');
            $receiptsUsd = $receiptsToday->where('currency', 'usd')->sum('amount');
            
            $purchasesToday = Purchase::whereDate('purchase_date', $today)->get();
            $purchasesSyp = $purchasesToday->where('currency', 'syp')->sum('amount');
            $purchasesUsd = $purchasesToday->where('currency', 'usd')->sum('amount');
            
            // حساب الديون لنا (استثناء الطلبات الملغاة)
            $orders = Order::with(['items', 'receipts', 'discounts'])
                ->whereNotIn('status', ['cancelled'])
                ->get();
            $debtsToUsSyp = 0;
            $debtsToUsUsd = 0;

            foreach ($orders as $order) {
                $totalSyp = $order->total_cost_syp;
                $totalUsd = $order->total_cost_usd;
                $paidSyp = $order->receipts->where('currency', 'syp')->sum('amount');
                $paidUsd = $order->receipts->where('currency', 'usd')->sum('amount');
                
                $debtSyp = $totalSyp - $paidSyp;
                $debtUsd = $totalUsd - $paidUsd;
                
                if ($debtSyp > 0) $debtsToUsSyp += $debtSyp;
                if ($debtUsd > 0) $debtsToUsUsd += $debtUsd;
            }
            
            $debtsOnUsSyp = Purchase::where('status', 'debt')->where('currency', 'syp')->sum('amount');
            $debtsOnUsUsd = Purchase::where('status', 'debt')->where('currency', 'usd')->sum('amount');
        
            // إعداد الرسالة
            $message = "📊 *التقرير اليومي*\n";
            $message .= "📅 " . $today->format('Y-m-d') . "\n\n";
        
            $message .= "🎯 *إحصائيات الطلبات:*\n";
            $message .= "• جديدة: {$ordersNew}\n";
            $message .= "• قيد التنفيذ: {$ordersInProgress}\n";
            $message .= "• جاهزة: {$ordersReady}\n";
            $message .= "• تم التسليم اليوم: {$ordersDelivered}\n";
            $message .= "• طلبات جديدة اليوم: {$newOrders}\n\n";
        
            // الطلبات الجديدة
            if ($newOrdersList->count() > 0) {
                $message .= "🆕 *الطلبات الجديدة:*\n";
                foreach ($newOrdersList->take(5) as $order) {
                    $urgentMark = $order->is_urgent ? "⚡" : "";
                    $message .= "{$urgentMark}#{$order->order_number} - {$order->customer_name}\n";
                }
                if ($newOrdersList->count() > 5) {
                    $message .= "... و" . ($newOrdersList->count() - 5) . " طلبية أخرى\n";
                }
                $message .= "\n";
            }
        
            // الطلبات قيد التنفيذ
            if ($inProgressList->count() > 0) {
                $message .= "⏳ *قيد التنفيذ:*\n";
                foreach ($inProgressList->take(5) as $order) {
                    $urgentMark = $order->is_urgent ? "⚡" : "";
                    $message .= "{$urgentMark}#{$order->order_number} - {$order->customer_name}\n";
                }
                if ($inProgressList->count() > 5) {
                    $message .= "... و" . ($inProgressList->count() - 5) . " طلبية أخرى\n";
                }
                $message .= "\n";
            }
        
            // الطلبات المتأخرة
            if ($oldInProgress->count() > 0) {
                $message .= "⚠️ *طلبات متأخرة (+3 أيام):*\n";
                foreach ($oldInProgress as $order) {
                    $referenceDate = $order->delivery_date ?? $order->updated_at;
                    $days = (int) Carbon::parse($referenceDate)->diffInDays(Carbon::now());
                    $urgentMark = $order->is_urgent ? "⚡" : "";
                    $message .= "{$urgentMark}#{$order->order_number} - {$order->customer_name} ({$days} يوم)\n";
                }
                $message .= "\n";
            }
        
            $message .= "💰 *المقبوضات اليوم:*\n";
            if ($receiptsSyp > 0) $message .= "• " . number_format($receiptsSyp, 0) . " ل.س\n";
            if ($receiptsUsd > 0) $message .= "• " . number_format($receiptsUsd, 2) . " $\n";
            if ($receiptsSyp == 0 && $receiptsUsd == 0) $message .= "• لا توجد مقبوضات\n";
            $message .= "\n";
            
            $message .= "🛒 *المشتريات اليوم:*\n";
            if ($purchasesSyp > 0) $message .= "• " . number_format($purchasesSyp, 0) . " ل.س\n";
            if ($purchasesUsd > 0) $message .= "• " . number_format($purchasesUsd, 2) . " $\n";
            if ($purchasesSyp == 0 && $purchasesUsd == 0) $message .= "• لا توجد مشتريات\n";
            $message .= "\n";
            
            $message .= "📈 *الديون لنا:*\n";
            if ($debtsToUsSyp > 0) $message .= "• " . number_format($debtsToUsSyp, 0) . " ل.س\n";
            if ($debtsToUsUsd > 0) $message .= "• " . number_format($debtsToUsUsd, 2) . " $\n";
            if ($debtsToUsSyp == 0 && $debtsToUsUsd == 0) $message .= "• لا توجد ديون\n";
            $message .= "\n";
            
            $message .= "📉 *الديون علينا:*\n";
            if ($debtsOnUsSyp > 0) $message .= "• " . number_format($debtsOnUsSyp, 0) . " ل.س\n";
            if ($debtsOnUsUsd > 0) $message .= "• " . number_format($debtsOnUsUsd, 2) . " $\n";
            if ($debtsOnUsSyp == 0 && $debtsOnUsUsd == 0) $message .= "• لا توجد ديون\n";
            
            // إضافة رابط التقرير المفصل
            $reportUrl = 'https://ayazgraphics.shop/public/reports/daily';
            $message .= "\n🔗 [عرض التقرير المفصل]({$reportUrl})";
        
            // إرسال الرسالة
            $response = Http::timeout(30)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            if ($response->successful()) {
                $this->info('تم إرسال التقرير بنجاح');
                Log::info('Daily report sent successfully to Telegram');
                return 0;
            } else {
                $this->error('فشل إرسال التقرير: ' . $response->body());
                Log::error('Failed to send daily report to Telegram', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('خطأ: ' . $e->getMessage());
            Log::error('Exception while sending daily report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
