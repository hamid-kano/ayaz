@extends('layouts.app')

@section('title', 'الملف الشخصي - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('home') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>الملف الشخصي</h2>
</div>

<div class="profile-container">
    <!-- Profile Picture Section -->
    <div class="profile-picture-section" x-data="profilePicture()">
        <div class="profile-avatar">
            <img x-ref="avatarImg" src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="الصورة الشخصية">
            <div class="avatar-overlay" @click="$refs.fileInput.click()">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <input type="file" x-ref="fileInput" @change="uploadAvatar($event)" accept="image/*" style="display: none;">
        <h3>{{ auth()->user()->name }}</h3>
        <p>{{ auth()->user()->email }}</p>
    </div>

    <!-- Password Change Section -->
    <div class="password-section">
        <div class="section-header">
            <i class="fas fa-lock"></i>
            <h3>تغيير كلمة المرور</h3>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="password-form">
            @csrf
            <div class="form-group">
                <label>كلمة المرور الحالية</label>
                <div class="input-group">
                    <input type="password" name="current_password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <div class="input-group">
                    <input type="password" name="password" required>
                    <i class="fas fa-key input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label>تأكيد كلمة المرور الجديدة</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" required>
                    <i class="fas fa-key input-icon"></i>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i>
                حفظ كلمة المرور
            </button>
        </form>
    </div>
</div>

<script>
function profilePicture() {
    return {
        async uploadAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch('{{ route("profile.avatar") }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    this.$refs.avatarImg.src = data.avatar_url;
                    toast.success('تم تحديث الصورة الشخصية بنجاح');
                } else {
                    toast.error('خطأ في تحديث الصورة الشخصية');
                }
            } catch (error) {
                toast.error('خطأ في الشبكة');
            }
        }
    }
}
</script>
@endsection