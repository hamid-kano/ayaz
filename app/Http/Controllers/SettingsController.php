<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'notification_hours_before' => Setting::get('notification_hours_before', 24),
            'notification_enabled' => Setting::get('notification_enabled', true),
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'notification_hours_before' => 'required|integer|min:1|max:168',
            'notification_enabled' => 'boolean',
        ]);

        Setting::set('notification_hours_before', $request->notification_hours_before);
        Setting::set('notification_enabled', $request->has('notification_enabled'));

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}