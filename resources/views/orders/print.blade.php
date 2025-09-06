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
            color: #333;
            background: white;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 16px;
            color: #718096;
        }
        
        .order-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            color: #667eea;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-group {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-right: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #2d3748;
        }
        
        .details-section {
            margin: 30px 0;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .details-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            line-height: 1.8;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-new { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-in-progress { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-delivered { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .cost-highlight {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            color: #718096;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000;
        }
        
        @media print {
            .print-btn { display: none; }
            body { margin: 0; }
            .print-container { max-width: none; margin: 0; padding: 0; }
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
        
        <div class="order-title">تفاصيل الطلبية #{{ $order->order_number }}</div>
        
        <div class="order-info">
            <div class="info-group">
                <div class="info-label">رقم الطلبية</div>
                <div class="info-value">#{{ $order->order_number }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">تاريخ الطلب</div>
                <div class="info-value">{{ $order->order_date->format('Y-m-d') }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">اسم العميل</div>
                <div class="info-value">{{ $order->customer_name }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">نوع الطلبية</div>
                <div class="info-value">{{ $order->order_type }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">الكلفة الإجمالية</div>
                <div class="info-value cost-highlight">
                    {{ number_format($order->cost, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}
                </div>
            </div>
            
            <div class="info-group">
                <div class="info-label">حالة الطلبية</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('new') جديدة @break
                            @case('in-progress') قيد التنفيذ @break
                            @case('delivered') تم التسليم @break
                            @case('cancelled') ملغاة @break
                        @endswitch
                    </span>
                </div>
            </div>
            
            <div class="info-group">
                <div class="info-label">تاريخ التسليم</div>
                <div class="info-value">{{ $order->delivery_date->format('Y-m-d') }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">المنفذ</div>
                <div class="info-value">{{ $order->executor->name ?? 'غير محدد' }}</div>
            </div>
        </div>
        
        <div class="details-section">
            <div class="section-title">تفاصيل الطلبية</div>
            <div class="details-content">
                {{ $order->order_details }}
            </div>
        </div>
        
        @if($order->receipts->count() > 0)
        <div class="details-section">
            <div class="section-title">المدفوعات</div>
            <div class="details-content">
                @foreach($order->receipts as $receipt)
                    <div style="margin-bottom: 10px;">
                        • {{ number_format($receipt->amount, 2) }} {{ $receipt->currency == 'usd' ? 'دولار' : 'ليرة' }} 
                        - {{ $receipt->receipt_date->format('Y-m-d') }}
                    </div>
                @endforeach
                <hr style="margin: 15px 0;">
                <div style="font-weight: bold;">
                    المبلغ المتبقي: {{ number_format($order->remaining_amount, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}
                </div>
            </div>
        </div>
        @endif
        
        <div class="footer">
            <p>تم طباعة هذا المستند في {{ now()->format('Y-m-d H:i') }}</p>
            <p>مطبعة ريناس - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>