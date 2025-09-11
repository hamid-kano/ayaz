<?php

namespace Database\Seeders;

use App\Models\Receipt;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReceiptSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::all();
        
        $receipts = [
            [
                'order_id' => $orders->where('order_number', 'ORD-001')->first()->id,
                'amount' => 200.00,
                'currency' => 'usd',
                'receipt_date' => Carbon::now()->subDays(3),
                'notes' => 'دفعة أولى من الزبون',
            ],
            [
                'order_id' => $orders->where('order_number', 'ORD-002')->first()->id,
                'amount' => 100000.00,
                'currency' => 'syp',
                'receipt_date' => Carbon::now()->subDays(2),
                'notes' => 'دفعة جزئية',
            ],
            [
                'order_id' => $orders->where('order_number', 'ORD-003')->first()->id,
                'amount' => 180.00,
                'currency' => 'usd',
                'receipt_date' => Carbon::now()->subDay(),
                'notes' => 'دفع كامل عند التسليم',
            ],
            [
                'order_id' => $orders->where('order_number', 'ORD-006')->first()->id,
                'amount' => 150.00,
                'currency' => 'usd',
                'receipt_date' => Carbon::now(),
                'notes' => 'دفعة مقدمة',
            ],
        ];

        foreach ($receipts as $receiptData) {
            Receipt::create($receiptData);
        }
    }
}