<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    public static function createForUser($userId, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function createForAllUsers($type, $title, $message, $data = null)
    {
        $users = User::where('is_active', true)->get();
        
        foreach ($users as $user) {
            self::createForUser($user->id, $type, $title, $message, $data);
        }
    }

    public static function newOrder($userId, $orderNumber, $customerName)
    {
        return self::createForUser(
            $userId,
            'new_order',
            'طلبية جديدة',
            "طلبية جديدة #{$orderNumber} من {$customerName}",
            ['order_number' => $orderNumber, 'customer' => $customerName]
        );
    }

    public static function deliveryReminder($userId, $orderNumber, $deliveryDate)
    {
        return self::createForUser(
            $userId,
            'delivery_reminder',
            'موعد تسليم',
            "موعد تسليم طلبية #{$orderNumber} في {$deliveryDate}",
            ['order_number' => $orderNumber, 'delivery_date' => $deliveryDate]
        );
    }

    public static function orderCompleted($userId, $orderNumber)
    {
        return self::createForUser(
            $userId,
            'order_completed',
            'تم التسليم',
            "تم تسليم طلبية #{$orderNumber} بنجاح",
            ['order_number' => $orderNumber]
        );
    }

    public static function paymentReceived($userId, $amount, $customerName)
    {
        return self::createForUser(
            $userId,
            'payment_received',
            'دفعة جديدة',
            "تم استلام دفعة {$amount} ريال من {$customerName}",
            ['amount' => $amount, 'customer' => $customerName]
        );
    }

    public static function debtReminder($userId, $amount, $customerName)
    {
        return self::createForUser(
            $userId,
            'debt_reminder',
            'تذكير دين',
            "دين مستحق من {$customerName} - {$amount} ريال",
            ['amount' => $amount, 'customer' => $customerName]
        );
    }
}