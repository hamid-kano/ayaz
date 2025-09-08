<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة الطلبية #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .invoice {
            width: 190mm;
            max-width: 190mm;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
        }

        .header {
            background: #006400;
            padding: 10px;
            text-align: center;
            border-bottom: 2px solid #000;
            color: white;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 12px;
            color: white;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #000;
        }

        .customer-info {
            text-align: right;
        }

        .invoice-number {
            text-align: left;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .items-table th {
            background: #006400;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            color: white;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            height: 30px;
            vertical-align: middle;
        }

        .item-name {
            font-weight: 500;
            color: #000;
        }

        .item-total {
            font-weight: 600;
            color: #006400;
        }

        .separator-row {
            border-top: 2px solid #006400 !important;
        }

        .empty-row {
            color: #ccc;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-top: 1px solid #000;
        }

        @media print {
            @page { size: A4; margin: 10mm; }
            body { margin: 0; }
            .invoice { width: 190mm; margin: 0; border: 2px solid #000; }
        }
    </style>
</head>

<body>

    <div class="invoice">
        <div class="header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 20px;">
                <img src="{{ asset('images/logo.png') }}" alt="لوغو" style="width: 60px; height: 60px; border-radius: 50%;">
                <div>
                    <div class="company-name">Renas Print</div>
                    <div class="company-info">
                        دعاية . طباعة . اعلان . زينة سيارات<br>
                        آياز قرموطي ٠٩٩٣١٤٧٢٤٤
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-details">
            <div class="customer-info">
                <div><strong>الزبون:</strong> {{ $order->customer_name }}</div>
                @if($order->customer_phone)
                <div><strong>الهاتف:</strong> {{ $order->customer_phone }}</div>
                @endif
                <div><strong>التاريخ:</strong> {{ $order->order_date->format('d/m/Y') }}</div>
            </div>
            <div class="invoice-number">
                <div><strong>رقم الفاتورة:</strong> {{ $order->order_number }}</div>
                <div><strong>تاريخ التسليم:</strong> {{ $order->delivery_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">م</th>
                    <th style="width: 15%;">الكمية</th>
                    <th style="width: 15%;">السعر</th>
                    <th style="width: 47%;">البيان والمواصفات</th>
                    <th style="width: 15%;">القيمة الإجمالية</th>
                </tr>
            </thead>
            <tbody>
                @if($order->items->count() > 0)
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatAmount($item->price) }}</td>
                        <td style="text-align: center;" class="item-name">{{ $item->item_name }}</td>
                        <td class="item-total">{{ \App\Helpers\TranslationHelper::formatAmount($item->quantity * $item->price) }}</td>
                    </tr>
                    @endforeach
                    @php
                        $remainingRows = 15 - $order->items->count();
                    @endphp
                    @for($i = 1; $i <= $remainingRows; $i++)
                    <tr>
                        <td>{{ $order->items->count() + $i }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                @else
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }}</td>
                        <td style="text-align: center;">{{ $order->order_details }}</td>
                        <td>{{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }}</td>
                    </tr>
                    @for($i = 2; $i <= 15; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <div class="footer-section">
            <div><strong>خصم للعميل:</strong></div>
            <div><strong>المجموع:</strong> 
                @if($order->items->count() > 0)
                    {{ \App\Helpers\TranslationHelper::formatAmount($order->items->sum(function($item) { return $item->quantity * $item->price; })) }}
                @else
                    {{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }}
                @endif
                {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}
            </div>
        </div>
    </div>

    <script>
    window.onload = function() {
        window.print();
    };
    </script>
</body>
</html>