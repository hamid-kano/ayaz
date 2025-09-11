<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OneSignalService;

class TestNotificationController extends Controller
{
    private $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    public function index()
    {
        return view('test-notification');
    }

    public function send(Request $request)
    {
        $request->validate([
            'player_id' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500'
        ]);

        try {
            $result = $this->oneSignalService->sendToUser(
                $request->player_id,
                $request->title,
                $request->message
            );

            if ($result && isset($result['id'])) {
                return back()->with([
                    'success' => 'تم إرسال الإشعار بنجاح!',
                    'response' => $result
                ]);
            } else {
                return back()->with([
                    'error' => 'فشل في إرسال الإشعار',
                    'response' => $result ?: 'لا توجد استجابة من الخدمة'
                ]);
            }
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'خطأ في الإرسال: ' . $e->getMessage(),
                'response' => null
            ]);
        }
    }
}