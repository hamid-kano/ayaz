<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    private $appId;
    private $restApiKey;
    private $baseUrl = 'https://onesignal.com/api/v1';

    public function __construct()
    {
        $this->appId = config('services.onesignal.app_id');
        $this->restApiKey = config('services.onesignal.rest_api_key');
    }

    /**
     * إرسال إشعار لمستخدم واحد
     */
    public function sendToUser($playerId, $title, $message, $data = [])
    {
        return $this->sendNotification([
            'include_player_ids' => [$playerId],
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message],
            'data' => $data
        ]);
    }

    /**
     * إرسال إشعار لعدة مستخدمين
     */
    public function sendToUsers($playerIds, $title, $message, $data = [])
    {
        return $this->sendNotification([
            'include_player_ids' => $playerIds,
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message],
            'data' => $data
        ]);
    }

    /**
     * إرسال إشعار لجميع المستخدمين
     */
    public function sendToAll($title, $message, $data = [])
    {
        return $this->sendNotification([
            'included_segments' => ['All'],
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message],
            'data' => $data
        ]);
    }

    /**
     * إرسال الإشعار عبر OneSignal API
     */
    private function sendNotification($payload)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/notifications', array_merge($payload, [
                'app_id' => $this->appId
            ]));

            if ($response->successful()) {
                Log::info('OneSignal notification sent successfully', $response->json());
                return $response->json();
            }

            Log::error('OneSignal notification failed', $response->json());
            return false;
        } catch (\Exception $e) {
            Log::error('OneSignal error: ' . $e->getMessage());
            return false;
        }
    }
}