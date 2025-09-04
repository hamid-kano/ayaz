@extends('layouts.app')

@section('content')
<div class="page-header">
    <button class="back-btn" onclick="window.location.href='{{ route('users.index') }}'">
        <i data-lucide="arrow-right"></i>
    </button>
    <h2>تعديل المستخدم</h2>
    <div></div>
</div>

    <div class="form-container">
        <form method="POST" action="{{ route('users.update', $user) }}" class="form-card">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">الاسم</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور الجديدة (اتركها فارغة للاحتفاظ بالحالية)</label>
            <input type="password" id="password" name="password">
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="form-group">
            <label for="role">الصلاحية</label>
            <select id="role" name="role" required>
                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>مستخدم</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>مدير</option>
            </select>
            @error('role')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <span class="checkmark"></span>
                المستخدم نشط
            </label>
        </div>

        <div class="form-actions">
            <a href="{{ route('users.index') }}" class="btn-secondary">إلغاء</a>
            <button type="submit" class="submit-btn">حفظ التغييرات</button>
        </div>
        </form>
    </div>
</div>
@endsection