@extends('layouts.app')

@section('content')
<div class="page-header">
    <button class="back-btn" onclick="window.location.href='{{ route('dashboard') }}'">
        <i data-lucide="arrow-right"></i>
    </button>
    <h2>الإعدادات</h2>
    <div></div>
</div>

<div class="form-container">
    @if(session('success'))

    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="notification_enabled" {{ $settings['notification_enabled'] ? 'checked' : '' }}>
                <span class="checkmark"></span>
                تفعيل الإشعارات
            </label>
        </div>

        <div class="form-group">
            <label for="notification_hours_before">إرسال إشعار قبل موعد التسليم بـ (ساعة)</label>
            <select id="notification_hours_before" name="notification_hours_before" required>
                <option value="1" {{ $settings['notification_hours_before'] == 1 ? 'selected' : '' }}>ساعة واحدة</option>
                <option value="2" {{ $settings['notification_hours_before'] == 2 ? 'selected' : '' }}>ساعتين</option>
                <option value="4" {{ $settings['notification_hours_before'] == 4 ? 'selected' : '' }}>4 ساعات</option>
                <option value="6" {{ $settings['notification_hours_before'] == 6 ? 'selected' : '' }}>6 ساعات</option>
                <option value="12" {{ $settings['notification_hours_before'] == 12 ? 'selected' : '' }}>12 ساعة</option>
                <option value="24" {{ $settings['notification_hours_before'] == 24 ? 'selected' : '' }}>24 ساعة (يوم واحد)</option>
                <option value="48" {{ $settings['notification_hours_before'] == 48 ? 'selected' : '' }}>48 ساعة (يومين)</option>
                <option value="72" {{ $settings['notification_hours_before'] == 72 ? 'selected' : '' }}>72 ساعة (3 أيام)</option>
                <option value="168" {{ $settings['notification_hours_before'] == 168 ? 'selected' : '' }}>168 ساعة (أسبوع)</option>
            </select>
            @error('notification_hours_before')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('dashboard') }}" class="btn-secondary">إلغاء</a>
            <button type="submit" class="submit-btn">حفظ الإعدادات</button>
        </div>
    </form>
</div>
@endsection