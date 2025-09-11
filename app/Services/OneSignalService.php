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
        $payload = [
            'include_player_ids' => [$playerId],
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message]
        ];
        
        if (!empty($data)) {
            $payload['data'] = $data;
        }
        
        return $this->sendNotification($payload);
    }

    /**
     * إرسال إشعار لعدة مستخدمين
     */
    public function sendToUsers($playerIds, $title, $message, $data = [])
    {
        $payload = [
            'include_player_ids' => $playerIds,
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message]
        ];
        
        if (!empty($data)) {
            $payload['data'] = $data;
        }
        
        return $this->sendNotification($payload);
    }

    /**
     * إرسال إشعار لجميع المستخدمين
     */
    public function sendToAll($title, $message, $data = [])
    {
        $payload = [
            'included_segments' => ['All'],
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message]
        ];
        
        if (!empty($data)) {
            $payload['data'] = $data;
        }
        
        return $this->sendNotification($payload);
    }

    /**
     * إرسال الإشعار عبر OneSignal API
     */
    private function sendNotification($payload)
    {
        try {
            if (!$this->appId || !$this->restApiKey) {
                Log::error('OneSignal: Missing app_id or rest_api_key');
                return ['error' => 'Missing OneSignal configuration'];
            }

            $finalPayload = array_merge($payload, ['app_id' => $this->appId]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/notifications', $finalPayload);

            $responseData = $response->json();
            
            if ($response->successful()) {
                return $responseData;
            } else {
                Log::error('OneSignal API error', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                return $responseData ?: ['error' => 'HTTP ' . $response->status()];
            }
        } catch (\Exception $e) {
            Log::error('OneSignal exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}