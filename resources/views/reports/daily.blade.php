<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير اليومي - {{ \App\Helpers\TranslationHelper::formatDate($today) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #667eea; }
        .header h1 { color: #2d3748; font-size: 32px; margin-bottom: 10px; font-weight: 700; }
        .header p { color: #718096; font-size: 18px; font-weight: 500; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-card h3 { font-size: 14px; margin-bottom: 10px; opacity: 0.9; font-weight: 600; }
        .stat-card p { font-size: 28px; font-weight: 700; }
        .section { margin-bottom: 30px; }
        .section-title { background: #f7fafc; padding: 15px; border-right: 4px solid #667eea; margin-bottom: 15px; font-size: 20px; font-weight: 700; color: #2d3748; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #edf2f7; padding: 12px; text-align: right; font-weight: 600; color: #2d3748; border-bottom: 2px solid #cbd5e0; }
        .table td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #4a5568; font-weight: 500; }
        .table tr:hover { background: #f7fafc; }
        .amount { font-weight: 700; color: #059669; }
        .amount.negative { color: #dc2626; }
        .no-data { text-align: center; padding: 40px; color: #a0aec0; font-style: italic; font-weight: 500; }
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; padding: 20px; }
            .no-print { display: none; }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 التقرير اليومي</h1>
            <p>{{ \App\Helpers\TranslationHelper::formatDate($today) }}</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>طلبات جديدة</h3>
                <p>{{ $ordersStats['new'] }}</p>
            </div>
            <div class="stat-card">
                <h3>قيد التنفيذ</h3>
                <p>{{ $ordersStats['in_progress'] }}</p>
            </div>
            <div class="stat-card">
                <h3>جاهزة</h3>
                <p>{{ $ordersStats['ready'] }}</p>
            </div>
            <div class="stat-card">
                <h3>تم التسليم اليوم</h3>
                <p>{{ $ordersStats['delivered'] }}</p>
            </div>
        </div>

        @if($newOrders->count() > 0)
        <div class="section">
            <div class="section-title">🆕 الطلبات الجديدة اليوم ({{ $newOrders->count() }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>المبلغ الإجمالي</th>
                        <th>موعد التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newOrders as $order)
                    <tr>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_type }}</td>
                        <td class="amount">
                            @if($order->total_cost_syp > 0 && $order->total_cost_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @elseif($order->total_cost_syp > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س
                            @else
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @endif
                        </td>
                        <td>{{ \App\Helpers\TranslationHelper::formatDateTime($order->delivery_date, 'd/m H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($newOrdersList->count() > 0)
        <div class="section">
            <div class="section-title">🆕 الطلبات الجديدة ({{ $newOrdersList->count() }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>موعد التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newOrdersList as $order)
                    <tr>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_type }}</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatDateTime($order->delivery_date, 'd/m H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($inProgressList->count() > 0)
        <div class="section">
            <div class="section-title">⏳ قيد التنفيذ ({{ $inProgressList->count() }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>موعد التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inProgressList as $order)
                    <tr>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_type }}</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatDateTime($order->delivery_date, 'd/m H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($oldInProgress->count() > 0)
        <div class="section">
            <div class="section-title">⚠️ طلبات متأخرة (+3 أيام) ({{ $oldInProgress->count() }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>موعد التسليم</th>
                        <th>عدد الأيام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($oldInProgress as $order)
                    <tr style="background: #fff3cd;">
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_type }}</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatDateTime($order->delivery_date, 'd/m H:i') }}</td>
                        <td><strong>{{ (int) \Carbon\Carbon::parse($order->updated_at)->diffInDays(\Carbon\Carbon::now()) }} يوم</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="section">
            <div class="section-title">💰 المقبوضات اليوم</div>
            @if($receiptsToday->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receiptsToday as $receipt)
                    <tr>
                        <td>#{{ $receipt->order->order_number }}</td>
                        <td>{{ $receipt->order->customer_name }}</td>
                        <td class="amount">{{ \App\Helpers\TranslationHelper::formatAmount($receipt->amount) }} {{ $receipt->currency == 'usd' ? '$' : 'ل.س' }}</td>
                        <td>{{ $receipt->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f7fafc; font-weight: bold;">
                        <td colspan="2">الإجمالي</td>
                        <td class="amount">
                            @if($receiptsSyp > 0) {{ \App\Helpers\TranslationHelper::formatAmount($receiptsSyp) }} ل.س @endif
                            @if($receiptsSyp > 0 && $receiptsUsd > 0) + @endif
                            @if($receiptsUsd > 0) {{ \App\Helpers\TranslationHelper::formatAmount($receiptsUsd) }} $ @endif
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">لا توجد مقبوضات اليوم</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">🛒 المشتريات اليوم</div>
            @if($purchasesToday->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>المورد</th>
                        <th>التفاصيل</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchasesToday as $purchase)
                    <tr>
                        <td>{{ $purchase->supplier_name }}</td>
                        <td>{{ $purchase->details }}</td>
                        <td class="amount negative">{{ \App\Helpers\TranslationHelper::formatAmount($purchase->amount) }} {{ $purchase->currency == 'usd' ? '$' : 'ل.س' }}</td>
                        <td>{{ $purchase->status == 'cash' ? 'نقداً' : 'دين' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f7fafc; font-weight: bold;">
                        <td colspan="2">الإجمالي</td>
                        <td class="amount negative">
                            @if($purchasesSyp > 0) {{ \App\Helpers\TranslationHelper::formatAmount($purchasesSyp) }} ل.س @endif
                            @if($purchasesSyp > 0 && $purchasesUsd > 0) + @endif
                            @if($purchasesUsd > 0) {{ \App\Helpers\TranslationHelper::formatAmount($purchasesUsd) }} $ @endif
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">لا توجد مشتريات اليوم</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">📈 الديون لنا</div>
            @if($debtsToUs->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الطلبية</th>
                        <th>العميل</th>
                        <th>المبلغ المتبقي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debtsToUs->take(10) as $debt)
                    <tr>
                        <td>#{{ $debt->order_number }}</td>
                        <td>{{ $debt->customer_name }}</td>
                        <td class="amount">
                            @if(isset($debt->debt_syp) && isset($debt->debt_usd))
                                @if($debt->debt_syp > 0 && $debt->debt_usd > 0)
                                    {{ \App\Helpers\TranslationHelper::formatAmount($debt->debt_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($debt->debt_usd) }} $
                                @elseif($debt->debt_syp > 0)
                                    {{ \App\Helpers\TranslationHelper::formatAmount($debt->debt_syp) }} ل.س
                                @else
                                    {{ \App\Helpers\TranslationHelper::formatAmount($debt->debt_usd) }} $
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background: #f7fafc; font-weight: bold;">
                        <td colspan="2">الإجمالي ({{ $debtsToUs->count() }} طلبية)</td>
                        <td class="amount">
                            @if($debtsToUsSyp > 0) {{ \App\Helpers\TranslationHelper::formatAmount($debtsToUsSyp) }} ل.س @endif
                            @if($debtsToUsSyp > 0 && $debtsToUsUsd > 0) + @endif
                            @if($debtsToUsUsd > 0) {{ \App\Helpers\TranslationHelper::formatAmount($debtsToUsUsd) }} $ @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">لا توجد ديون</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">📉 الديون علينا</div>
            @if($debtsOnUs->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>المورد</th>
                        <th>التفاصيل</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debtsOnUs->take(10) as $debt)
                    <tr>
                        <td>{{ $debt->supplier_name }}</td>
                        <td>{{ $debt->details }}</td>
                        <td class="amount negative">{{ \App\Helpers\TranslationHelper::formatAmount($debt->amount) }} {{ $debt->currency == 'usd' ? '$' : 'ل.س' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f7fafc; font-weight: bold;">
                        <td colspan="2">الإجمالي ({{ $debtsOnUs->count() }} مشترى)</td>
                        <td class="amount negative">
                            @if($debtsOnUsSyp > 0) {{ \App\Helpers\TranslationHelper::formatAmount($debtsOnUsSyp) }} ل.س @endif
                            @if($debtsOnUsSyp > 0 && $debtsOnUsUsd > 0) + @endif
                            @if($debtsOnUsUsd > 0) {{ \App\Helpers\TranslationHelper::formatAmount($debtsOnUsUsd) }} $ @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">لا توجد ديون</div>
            @endif
        </div>

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold;">
                🖨️ طباعة التقرير
            </button>
        </div>
    </div>
</body>
</html>
