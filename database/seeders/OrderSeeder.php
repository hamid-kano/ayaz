<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        
        $orders = [
            [
                'order_number' => 'ORD-001',
                'order_date' => Carbon::now()->subDays(5),
                'customer_name' => 'أحمد محمد',
                'order_type' => 'بطاقات عمل',
                'order_details' => '1000 بطاقة عمل ملونة مع تصميم خاص',
                'status' => 'new',
                'is_urgent' => true,
                'delivery_date' => Carbon::now()->addDays(3),
                'reviewer_name' => 'علي أحمد',
                'executor_id' => $users->skip(1)->first()->id,
            ],
            [
                'order_number' => 'ORD-002',
                'order_date' => Carbon::now()->subDays(4),
                'customer_name' => 'فاطمة علي',
                'order_type' => 'فلايرز إعلانية',
                'order_details' => '500 فلاير إعلاني A4 ملون',
                'status' => 'in-progress',
                'is_urgent' => false,
                'delivery_date' => Carbon::now()->addDays(2),
                'reviewer_name' => 'محمد خالد',
                'executor_id' => $users->last()->id,
            ],
            [
                'order_number' => 'ORD-003',
                'order_date' => Carbon::now()->subDays(3),
                'customer_name' => 'عمر خالد',
                'order_type' => 'بروشورات',
                'order_details' => '200 بروشور ثلاثي الطي',
                'status' => 'delivered',
                'is_urgent' => false,
                'delivery_date' => Carbon::now()->subDay(),
                'reviewer_name' => 'سارة محمد',
                'executor_id' => $users->first()->id,
            ],
            [
                'order_number' => 'ORD-004',
                'order_date' => Carbon::now()->subDays(2),
                'customer_name' => 'سارة أحمد',
                'order_type' => 'لافتات إعلانية',
                'order_details' => 'لافتة إعلانية كبيرة 3x2 متر',
                'status' => 'cancelled',
                'is_urgent' => true,
                'delivery_date' => Carbon::now()->addDays(5),
                'reviewer_name' => 'أحمد علي',
                'executor_id' => $users->skip(1)->first()->id,
            ],
            [
                'order_number' => 'ORD-005',
                'order_date' => Carbon::now()->subDay(),
                'customer_name' => 'محمد حسن',
                'order_type' => 'كتب ومجلات',
                'order_details' => 'كتاب 100 صفحة مع غلاف ملون',
                'status' => 'new',
                'is_urgent' => false,
                'delivery_date' => Carbon::now()->addDays(7),
                'reviewer_name' => 'نور الدين',
                'executor_id' => $users->last()->id,
            ],
            [
                'order_number' => 'ORD-006',
                'order_date' => Carbon::now(),
                'customer_name' => 'نور الدين',
                'order_type' => 'بطاقات عمل فاخرة',
                'order_details' => '500 بطاقة عمل فاخرة مع طباعة ذهبية',
                'status' => 'in-progress',
                'is_urgent' => true,
                'delivery_date' => Carbon::now()->addDays(4),
                'reviewer_name' => 'فاطمة حسن',
                'executor_id' => $users->first()->id,
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }
    }
}