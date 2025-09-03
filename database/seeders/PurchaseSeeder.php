<?php

namespace Database\Seeders;

use App\Models\Purchase;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run()
    {
        $purchases = [
            [
                'purchase_number' => 'PUR-001',
                'amount' => 500.00,
                'currency' => 'usd',
                'purchase_date' => Carbon::now()->subDays(10),
                'status' => 'cash',
                'details' => 'ورق طباعة A4 - 100 علبة',
                'supplier' => 'مورد الورق المحدود',
            ],
            [
                'purchase_number' => 'PUR-002',
                'amount' => 2500000.00,
                'currency' => 'syp',
                'purchase_date' => Carbon::now()->subDays(8),
                'status' => 'debt',
                'details' => 'أحبار ملونة متنوعة للطابعات',
                'supplier' => 'شركة الأحبار الذهبية',
            ],
            [
                'purchase_number' => 'PUR-003',
                'amount' => 300.00,
                'currency' => 'usd',
                'purchase_date' => Carbon::now()->subDays(6),
                'status' => 'cash',
                'details' => 'قطع غيار للطابعة الرئيسية',
                'supplier' => 'معدات الطباعة الحديثة',
            ],
            [
                'purchase_number' => 'PUR-004',
                'amount' => 800000.00,
                'currency' => 'syp',
                'purchase_date' => Carbon::now()->subDays(4),
                'status' => 'debt',
                'details' => 'مواد تجليد وتغليف متنوعة',
                'supplier' => 'مكتبة الرسائل',
            ],
            [
                'purchase_number' => 'PUR-005',
                'amount' => 150.00,
                'currency' => 'usd',
                'purchase_date' => Carbon::now()->subDays(2),
                'status' => 'cash',
                'details' => 'أدوات قطع وتشطيب',
                'supplier' => 'أدوات الطباعة المتقدمة',
            ],
            [
                'purchase_number' => 'PUR-006',
                'amount' => 800.00,
                'currency' => 'usd',
                'purchase_date' => Carbon::now()->subDay(),
                'status' => 'debt',
                'details' => 'آلة طباعة صغيرة للأعمال السريعة',
                'supplier' => 'معدات الطباعة المتقدمة',
            ],
        ];

        foreach ($purchases as $purchaseData) {
            Purchase::create($purchaseData);
        }
    }
}