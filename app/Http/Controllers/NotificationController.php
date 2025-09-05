<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private $oneSignal;

    public function __construct(OneSignalService $oneSignal = null)
    {
        $this->oneSignal = $oneSignal;
    }

    // Local Notifications Methods
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        try {
            $count = Auth::user()->notifications()->unread()->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function getRecent()
    {
        try {
            $notifications = Auth::user()->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'read' => $notification->isRead(),
                        'time' => $notification->created_at->diffForHumans(),
                        'icon' => $this->getIconForType($notification->type)
                    ];
                });

            $unreadCount = Auth::user()->notifications()->unread()->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $unreadCount = Auth::user()->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    // OneSignal Push Notifications Methods
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        $user = User::find($request->user_id);
        
        // Create local notification
        Notification::create([
            'user_id' => $user->id,
            'type' => $request->data['type'] ?? 'general',
            'title' => $request->title,
            'message' => $request->message,
            'data' => $request->data
        ]);

        // Send push notification if OneSignal service available and user has player_id
        if ($this->oneSignal && $user->player_id) {
            $this->oneSignal->sendToUser(
                $user->player_id,
                $request->title,
                $request->message,
                $request->data ?? []
            );
        }

        return response()->json(['success' => true]);
    }

    public function sendToUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        // Create local notifications for all users
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $request->data['type'] ?? 'general',
                'title' => $request->title,
                'message' => $request->message,
                'data' => $request->data
            ]);
        }

        // Send push notifications if OneSignal available
        if ($this->oneSignal) {
            $playerIds = $users->whereNotNull('player_id')->pluck('player_id')->toArray();
            if (!empty($playerIds)) {
                $this->oneSignal->sendToUsers(
                    $playerIds,
                    $request->title,
                    $request->message,
                    $request->data ?? []
                );
            }
        }

        return response()->json(['success' => true]);
    }

    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        // Create local notifications for all active users
        $users = User::where('is_active', true)->get();
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $request->data['type'] ?? 'general',
                'title' => $request->title,
                'message' => $request->message,
                'data' => $request->data
            ]);
        }

        // Send push notification to all if OneSignal available
        if ($this->oneSignal) {
            $this->oneSignal->sendToAll(
                $request->title,
                $request->message,
                $request->data ?? []
            );
        }

        return response()->json(['success' => true]);
    }

    private function getIconForType($type)
    {
        return match($type) {
            'new_order' => 'package',
            'delivery_reminder' => 'clock',
            'order_completed' => 'check-circle',
            'payment_received' => 'banknote',
            'debt_reminder' => 'alert-circle',
            default => 'bell'
        };
    }
}