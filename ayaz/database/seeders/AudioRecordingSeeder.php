<?php

namespace Database\Seeders;

use App\Models\AudioRecording;
use App\Models\Order;
use Illuminate\Database\Seeder;

class AudioRecordingSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::all();
        
        $audioRecordings = [
            [
                'order_id' => $orders->first()->id,
                'file_name' => 'تسجيل 1 - تفاصيل البطاقة.wav',
                'file_path' => 'audio/recording1.wav',
                'duration' => 45,
            ],
            [
                'order_id' => $orders->first()->id,
                'file_name' => 'تسجيل 2 - ملاحظات إضافية.wav',
                'file_path' => 'audio/recording2.wav',
                'duration' => 32,
            ],
            [
                'order_id' => $orders->skip(1)->first()->id,
                'file_name' => 'تسجيل - مواصفات الفلاير.wav',
                'file_path' => 'audio/recording3.wav',
                'duration' => 67,
            ],
            [
                'order_id' => $orders->skip(4)->first()->id,
                'file_name' => 'تسجيل - تعليمات الطباعة.wav',
                'file_path' => 'audio/recording4.wav',
                'duration' => 28,
            ],
        ];

        foreach ($audioRecordings as $audioData) {
            AudioRecording::create($audioData);
        }
    }
}