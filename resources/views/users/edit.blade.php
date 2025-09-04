@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="header">
        <h1>تعديل المستخدم</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i data-lucide="arrow-right"></i>
            العودة
        </a>
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
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
@endsection