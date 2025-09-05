<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    /**
     * إرسال إشعار لمستخدم واحد
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        $user = User::find($request->user_id);
        
        if (!$user->player_id) {
            return response()->json(['error' => 'المستخدم لا يملك player_id'], 400);
        }

        $result = $this->oneSignal->sendToUser(
            $user->player_id,
            $request->title,
            $request->message,
            $request->data ?? []
        );

        return response()->json($result ? ['success' => true] : ['error' => 'فشل في الإرسال']);
    }

    /**
     * إرسال إشعار لعدة مستخدمين
     */
    public function sendToUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        $playerIds = User::whereIn('id', $request->user_ids)
            ->whereNotNull('player_id')
            ->pluck('player_id')
            ->toArray();

        if (empty($playerIds)) {
            return response()->json(['error' => 'لا يوجد مستخدمين بـ player_id صالح'], 400);
        }

        $result = $this->oneSignal->sendToUsers(
            $playerIds,
            $request->title,
            $request->message,
            $request->data ?? []
        );

        return response()->json($result ? ['success' => true] : ['error' => 'فشل في الإرسال']);
    }

    /**
     * إرسال إشعار لجميع المستخدمين
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'data' => 'array'
        ]);

        $result = $this->oneSignal->sendToAll(
            $request->title,
            $request->message,
            $request->data ?? []
        );

        return response()->json($result ? ['success' => true] : ['error' => 'فشل في الإرسال']);
    }
}