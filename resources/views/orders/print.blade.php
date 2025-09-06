<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة الطلبية #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Arial', sans-serif;
            line-height: 1.6;
            color: #1a202c;
            background: #f7fafc;
        }

        .print-container {
            max-width: 900px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #764ba2;
        }

        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 10px;
            backdrop-filter: blur(10px);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .company-name {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .company-subtitle {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 500;
        }

        .content {
            padding: 10px;
        }

        .order-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            color: #2d3748;
            position: relative;
        }

        .order-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        .info-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .info-table td {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: #4a5568;
            background: #edf2f7;
            width: 35%;
        }

        .info-table td:last-child {
            color: #2d3748;
            font-weight: 500;
        }



        .cost-highlight {
            font-weight: bold;
            color: #000;
        }

        .details-section {
            margin: 40px 0;
            background: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, #4a5568, #2d3748);
            color: white;
            padding: 20px 24px;
            font-size: 18px;
            font-weight: 600;
        }

        .section-content {
            padding: 24px;
            line-height: 1.8;
            font-size: 16px;
        }

        .payments-list {
            list-style: none;
        }

        .payments-list li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payments-list li:last-child {
            border-bottom: none;
            font-weight: 600;
            background: #e6fffa;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
        }

        .footer {
            background: #2d3748;
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 14px;
        }

        .print-btn {
            position: fixed;
            top: 30px;
            left: 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Cairo', 'Arial', sans-serif;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                background: white;
                margin: 0;
            }

            .print-container {
                max-width: none;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .header::after {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button class="print-btn" onclick="window.print()">طباعة</button>

    <div class="print-container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="مطبعة ريناس">
            </div>
            <div class="company-name">مطبعة ريناس</div>
            <div class="company-subtitle">للطباعة والتصميم</div>
        </div>

        <div class="content">
            <div class="order-title">تفاصيل الطلبية #{{ $order->order_number }}</div>

            <table class="info-table">
                <tr>
                    <td>رقم الطلبية</td>
                    <td>#{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td>تاريخ الطلب</td>
                    <td>{{ $order->order_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td>اسم العميل</td>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                @if ($order->customer_phone)
                    <tr>
                        <td>رقم هاتف العميل</td>
                        <td>{{ $order->customer_phone }}</td>
                    </tr>
                @endif
                <tr>
                    <td>نوع الطلبية</td>
                    <td>{{ $order->order_type }}</td>
                </tr>
                <tr>
                    <td>الكلفة الإجمالية</td>
                    <td><span class="cost-highlight">{{ number_format($order->cost, 2) }}
                            {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span></td>
                </tr>

                <tr>
                    <td>تاريخ التسليم</td>
                    <td>{{ $order->delivery_date->format('Y-m-d') }}</td>
                </tr>

            </table>

            <div class="details-section">
                <div class="section-header">تفاصيل الطلبية</div>
                <div class="section-content">{{ $order->order_details }}</div>
            </div>

            @if ($order->receipts->count() > 0)
                <div class="details-section">
                    <div class="section-header">المدفوعات</div>
                    <div class="section-content">
                        <ul class="payments-list">
                            @foreach ($order->receipts as $receipt)
                                <li>
                                    <span>{{ $receipt->receipt_date->format('Y-m-d') }}</span>
                                    <span>{{ number_format($receipt->amount, 2) }}
                                        {{ $receipt->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
                                </li>
                            @endforeach
                            <li>
                                <span>المبلغ المتبقي</span>
                                <span>{{ number_format($order->remaining_amount, 2) }}
                                    {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>تم طباعة هذا المستند في {{ now()->format('Y-m-d H:i') }}</p>
            <p>مطبعة ريناس - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>

</html>
