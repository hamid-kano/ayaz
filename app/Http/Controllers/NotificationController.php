<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = [
            [
                'id' => 1,
                'type' => 'new_order',
                'title' => 'طلبية جديدة',
                'message' => 'طلبية جديدة من أحمد محمد',
                'icon' => 'package',
                'time' => 'منذ 5 دقائق',
                'read' => false,
                'created_at' => now()->subMinutes(5)
            ],
            [
                'id' => 2,
                'type' => 'delivery_reminder',
                'title' => 'موعد تسليم',
                'message' => 'موعد تسليم طلبية #1001',
                'icon' => 'clock',
                'time' => 'منذ ساعة',
                'read' => false,
                'created_at' => now()->subHour()
            ],
            [
                'id' => 3,
                'type' => 'order_completed',
                'title' => 'تم التسليم',
                'message' => 'تم تسليم طلبية #999',
                'icon' => 'check-circle',
                'time' => 'منذ 3 ساعات',
                'read' => true,
                'created_at' => now()->subHours(3)
            ],
            [
                'id' => 4,
                'type' => 'payment_received',
                'title' => 'دفعة جديدة',
                'message' => 'تم استلام دفعة 500 دولار',
                'icon' => 'banknote',
                'time' => 'منذ يوم',
                'read' => true,
                'created_at' => now()->subDay()
            ]
        ];

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        // Mark notification as read logic here
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        // Mark all notifications as read logic here
        return response()->json(['success' => true]);
    }
}