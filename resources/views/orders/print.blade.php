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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-top: 1px solid #000;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
            font-family: 'Cairo', Arial, sans-serif;
        }

        @media print {
            .print-btn { display: none; }
            @page { size: A4; margin: 10mm; }
            body { margin: 0; }
            .invoice { width: 190mm; margin: 0; border: 2px solid #000; }
        }
    </style>
</head>

<body>
    <button class="print-btn" onclick="printPage()">طباعة</button>

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
                <div><strong>العميل:</strong> {{ $order->customer_name }}</div>
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
                    <th style="width: 32%;">البيان والمواصفات</th>
                    <th style="width: 15%;">النوع الإجمالي</th>
                    <th style="width: 15%;">القيمة الإجمالية</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>1</td>
                    <td>{{ number_format($order->cost, 0) }}</td>
                    <td style="text-align: right; padding-right: 10px;">{{ $order->order_details }}</td>
                    <td>{{ $order->order_type }}</td>
                    <td>{{ number_format($order->cost, 0) }}</td>
                </tr>
                @for($i = 2; $i <= 15; $i++)
                <tr>
                    <td>{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="footer-section">
            <div><strong>خصم للعميل:</strong></div>
            <div><strong>المجموع:</strong> {{ number_format($order->cost, 0) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</div>
        </div>
    </div>

    <script>
    function printPage() {
        window.print();
    }
    </script>
</body>
</html>