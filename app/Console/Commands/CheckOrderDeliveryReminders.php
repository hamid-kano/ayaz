<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Notification;
use App\Services\OneSignalService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOrderDeliveryReminders extends Command
{
    protected $signature = 'orders:check-reminders';
    protected $description = 'Check orders and send delivery reminders based on settings';

    public function handle()
    {
        $notificationEnabled = Setting::get('notification_enabled', false);
        
        if (!$notificationEnabled) {
            $this->info('Notifications are disabled');
            return;
        }

        $hoursBefore = (int) Setting::get('notification_hours_before', 24);
        $reminderTime = Carbon::now()->addHours($hoursBefore);

        $orders = Order::where('status', '!=', 'delivered')
            ->where('status', '!=', 'cancelled')
            ->whereDate('delivery_date', $reminderTime->toDateString())
            ->whereTime('delivery_date', '<=', $reminderTime->toTimeString())
            ->whereDoesntHave('notifications', function($query) {
                $query->where('type', 'delivery_reminder')
                    ->where('created_at', '>=', Carbon::now()->subDay());
            })
            ->with('executor')
            ->get();

        $oneSignal = new OneSignalService();
        $count = 0;

        foreach ($orders as $order) {
            $urgentText = $order->is_urgent ? ' (مستعجلة)' : '';
            
            // إشعار للمنفذ
            if ($order->executor_id) {
                Notification::create([
                    'user_id' => $order->executor_id,
                    'type' => 'delivery_reminder',
                    'title' => 'تذكير بموعد التسليم' . $urgentText,
                    'message' => "موعد تسليم الطلبية {$order->order_number} خلال {$hoursBefore} ساعة" . $urgentText,
                    'data' => ['order_id' => $order->id]
                ]);

                if ($order->executor->player_id) {
                    try {
                        $response = $oneSignal->sendToUser(
                            $order->executor->player_id,
                            'تذكير بموعد التسليم' . $urgentText,
                            "موعد تسليم الطلبية {$order->order_number} خلال {$hoursBefore} ساعة" . $urgentText,
                            ['order_id' => $order->id, 'type' => 'delivery_reminder']
                        );
                        Log::info('OneSignal notification sent to executor', [
                            'order_id' => $order->id,
                            'executor_id' => $order->executor_id,
                            'player_id' => $order->executor->player_id,
                            'response' => $response
                        ]);
                    } catch (\Exception $e) {
                        Log::error('OneSignal notification failed for executor', [
                            'order_id' => $order->id,
                            'executor_id' => $order->executor_id,
                            'player_id' => $order->executor->player_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                $count++;
            }
            
            // إشعار للأدمن
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'delivery_reminder_admin',
                    'title' => 'تذكير بموعد تسليم' . $urgentText,
                    'message' => "موعد تسليم الطلبية {$order->order_number} للزبون {$order->customer_name} خلال {$hoursBefore} ساعة" . $urgentText,
                    'data' => ['order_id' => $order->id]
                ]);
                
                if ($admin->player_id) {
                    try {
                        $response = $oneSignal->sendToUser(
                            $admin->player_id,
                            'تذكير بموعد تسليم' . $urgentText,
                            "موعد تسليم الطلبية {$order->order_number} للزبون {$order->customer_name} خلال {$hoursBefore} ساعة" . $urgentText,
                            ['order_id' => $order->id, 'type' => 'delivery_reminder_admin']
                        );
                        Log::info('OneSignal notification sent to admin', [
                            'order_id' => $order->id,
                            'admin_id' => $admin->id,
                            'player_id' => $admin->player_id,
                            'response' => $response
                        ]);
                    } catch (\Exception $e) {
                        Log::error('OneSignal notification failed for admin', [
                            'order_id' => $order->id,
                            'admin_id' => $admin->id,
                            'player_id' => $admin->player_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        $this->info("Sent {$count} delivery reminders");
    }
}
