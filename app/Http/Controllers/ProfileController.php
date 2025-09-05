<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();
        
        // حذف الصورة القديمة
        if ($user->avatar && file_exists(public_path('profile/' . $user->avatar))) {
            unlink(public_path('profile/' . $user->avatar));
        }

        // رفع الصورة الجديدة
        $fileName = $user->id . '_' . time() . '.' . $request->avatar->extension();
        $request->avatar->move(public_path('profile'), $fileName);

        // تحديث قاعدة البيانات
        $user->update(['avatar' => $fileName]);

        return response()->json([
            'success' => true,
            'avatar_url' => asset('profile/' . $fileName)
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}