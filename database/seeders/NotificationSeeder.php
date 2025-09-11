<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // إشعارات غير مقروءة
            Notification::create([
                'user_id' => $user->id,
                'type' => 'new_order',
                'title' => 'طلبية جديدة',
                'message' => 'طلبية جديدة من أحمد محمد - طباعة كروت شخصية',
                'data' => ['order_id' => 1],
                'created_at' => now()->subMinutes(5)
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'delivery_reminder',
                'title' => 'موعد تسليم',
                'message' => 'موعد تسليم طلبية #1001 اليوم',
                'data' => ['order_id' => 1001],
                'created_at' => now()->subHour()
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'debt_reminder',
                'title' => 'تذكير دين',
                'message' => 'دين مستحق من زبون محمد علي - 250 ريال',
                'data' => ['debt_amount' => 250],
                'created_at' => now()->subHours(2)
            ]);

            // إشعارات مقروءة
            Notification::create([
                'user_id' => $user->id,
                'type' => 'order_completed',
                'title' => 'تم التسليم',
                'message' => 'تم تسليم طلبية #999 بنجاح',
                'data' => ['order_id' => 999],
                'read_at' => now()->subMinutes(30),
                'created_at' => now()->subHours(3)
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'payment_received',
                'title' => 'دفعة جديدة',
                'message' => 'تم استلام دفعة 500 ريال من زبون سارة أحمد',
                'data' => ['amount' => 500],
                'read_at' => now()->subHours(2),
                'created_at' => now()->subDay()
            ]);
        }
    }
}