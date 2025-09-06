<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Notification;
use App\Services\OneSignalService;
use Illuminate\Console\Command;
use Carbon\Carbon;

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

        $hoursBefore = Setting::get('notification_hours_before', 24);
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
            if ($order->executor_id) {
                Notification::create([
                    'user_id' => $order->executor_id,
                    'type' => 'delivery_reminder',
                    'title' => 'تذكير بموعد التسليم',
                    'message' => "موعد تسليم الطلبية {$order->order_number} خلال {$hoursBefore} ساعة",
                    'data' => ['order_id' => $order->id]
                ]);

                if ($order->executor->player_id) {
                    $oneSignal->sendToUser(
                        $order->executor->player_id,
                        'تذكير بموعد التسليم',
                        "موعد تسليم الطلبية {$order->order_number} خلال {$hoursBefore} ساعة",
                        ['order_id' => $order->id, 'type' => 'delivery_reminder']
                    );
                }
                $count++;
            }
        }

        $this->info("Sent {$count} delivery reminders");
    }
}
