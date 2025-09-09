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

        .order-details-section {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #000;
            text-align: right;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .order-details-header {
            font-weight: bold;
            color: #006400;
            font-size: 16px;
            flex-shrink: 0;
        }

        .order-details-content {
            color: #333;
            line-height: 1.6;
            font-size: 14px;
            flex: 1;
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
            #pdfBtn { display: none !important; }
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
        
        @if($order->order_details)
        <div class="order-details-section">
            <div class="order-details-header">تفاصيل الطلبية:</div>
            <div class="order-details-content">{{ $order->order_details }}</div>
        </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">م</th>
                    <th style="width: 40%;">اسم المادة</th>
                    <th style="width: 17%;">الكمية</th>
                    <th style="width: 17%;">السعر</th>
                    <th style="width: 18%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @if($order->items->count() > 0)
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: center;" class="item-name">{{ $item->item_name }}</td>
                        <td><strong>{{ $item->quantity }}</strong></td>
                        <td><strong>{{ \App\Helpers\TranslationHelper::formatAmount($item->price) }}
                                <small>{{ $item->currency == 'usd' ? '$' : 'ل.س' }}</small></strong>
                        </td>
                        <td class="item-total"><strong>{{ \App\Helpers\TranslationHelper::formatAmount($item->quantity * $item->price) }}
                                <small>{{ $item->currency == 'usd' ? '$' : 'ل.س' }}</small></strong>
                        </td>
                    </tr>
                    @endforeach
                    @php
                        $remainingRows = 15 - $order->items->count();
                    @endphp
                    @if($remainingRows > 0 && $order->items->count() > 0)
                        <tr class="separator-row">
                            <td>{{ $order->items->count() + 1 }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @for($i = 2; $i <= $remainingRows; $i++)
                        <tr class="empty-row">
                            <td>{{ $order->items->count() + $i }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endfor
                    @endif
                @else
                    <tr>
                        <td>1</td>
                        <td style="text-align: center;">طلبية عامة</td>
                        <td><strong>1</strong></td>
                        <td><strong>{{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }}</strong></td>
                        <td><strong>{{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }}</strong></td>
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
            <div></div>
            <div><strong>المجموع:</strong> 
                @if($order->items->count() > 0)
                    @php
                        $totalSyp = $order->items->where('currency', 'syp')->sum(function($item) { return $item->quantity * $item->price; });
                        $totalUsd = $order->items->where('currency', 'usd')->sum(function($item) { return $item->quantity * $item->price; });
                    @endphp
                    @if($totalSyp > 0 && $totalUsd > 0)
                        {{ \App\Helpers\TranslationHelper::formatAmount($totalSyp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($totalUsd) }} $
                    @elseif($totalSyp > 0)
                        {{ \App\Helpers\TranslationHelper::formatAmount($totalSyp) }} ل.س
                    @else
                        {{ \App\Helpers\TranslationHelper::formatAmount($totalUsd) }} $
                    @endif
                @else
                    {{ \App\Helpers\TranslationHelper::formatAmount($order->cost) }} {{ $order->currency == 'usd' ? '$' : 'ل.س' }}
                @endif
            </div>
        </div>
        
        <div class="page-footer">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; padding: 15px; background: #006400; color: white; font-size: 12px;">
                <div style="text-align: right;">
                    <div>الإدارة: ٠٩٩٣١٤٧٢٤٤</div>
                    <div>الاستعلامات: ٠٩٩٠٥٧٨٤٧١</div>
                </div>
                <div style="text-align: center;">
                    <div>تم طباعة هذا المستند في</div>
                    <div>{{ now()->format('Y-m-d H:i') }}</div>
                </div>
                <div style="text-align: left;">
                    <div>ديار: ٠٩٩٤٧٢٥٠٩٠</div>
                    <div>دلو الفرع 2: ٠٩٣٢٣٥٠٦٠١</div>
                </div>
            </div>
            <div style="text-align: center; padding: 8px; background: rgba(0,100,0,0.8); color: white;">
                <div style="font-weight: bold;">Instagram: renas_print</div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    function generatePDF() {
        const invoice = document.querySelector('.invoice');
        const button = document.querySelector('#pdfBtn');
        
        button.style.display = 'none';
        
        html2canvas(invoice, {
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 190;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            pdf.save('فاتورة-{{ $order->order_number }}.pdf');
            
            button.style.display = 'block';
        });
    }
    
    window.onload = function() {
        window.print();
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        const pdfBtn = document.createElement('button');
        pdfBtn.id = 'pdfBtn';
        pdfBtn.innerHTML = 'تصدير PDF';
        pdfBtn.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:9999;padding:15px 30px;background:#006400;color:white;border:none;border-radius:25px;cursor:pointer;font-size:16px;box-shadow:0 4px 8px rgba(0,0,0,0.3);font-family:"Cairo",Arial,sans-serif;';
        pdfBtn.onclick = generatePDF;
        document.body.appendChild(pdfBtn);
    });
    </script>
</body>
</html>