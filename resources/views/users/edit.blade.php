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
            <label for="player_id">معرف اللاعب</label>
            @if($user->player_id)
                <div class="player-id-display">
                    <input type="text" value="{{ $user->player_id }}" readonly onclick="this.select()">
                    <button type="button" onclick="copyPlayerID('{{ $user->player_id }}')" class="copy-btn">
                        <i data-lucide="copy"></i>
                    </button>
                </div>
            @else
                <div class="player-id-empty-form">غير موجود</div>
            @endif
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
                <option value="auditor" {{ old('role', $user->role) === 'auditor' ? 'selected' : '' }}>مدقق</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>مدير</option>
            </select>
            @error('role')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="is_active">حالة المستخدم</label>
            <select id="is_active" name="is_active" required>
                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>نشط</option>
                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>معطل</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="{{ route('users.index') }}" class="btn-secondary">إلغاء</a>
            <button type="submit" class="submit-btn">حفظ التغييرات</button>
        </div>
        </form>
    </div>
</div>

<script>
function copyPlayerID(playerId) {
    navigator.clipboard.writeText(playerId).then(() => {
        alert('تم نسخ معرف اللاعب');
    });
}
</script>

<style>
.player-id-display {
    display: flex;
    gap: 8px;
}
.player-id-display input {
    flex: 1;
    background: #f8f9fa;
    cursor: pointer;
}
.copy-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
}
.copy-btn:hover {
    background: #0056b3;
}
.player-id-empty-form {
    padding: 10px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #999;
    font-style: italic;
}
</style>
@endsection