<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - مطبعة ريناس</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/lucide.js') }}"></script>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i data-lucide="printer"></i>
                </div>
                <h1>مطبعة ريناس</h1>
                <p>إنشاء حساب جديد</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <div class="input-group">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسمك الكامل" required autofocus>
                        <i data-lucide="user" class="input-icon"></i>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required>
                        <i data-lucide="mail" class="input-icon"></i>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="أدخل كلمة مرور قوية" required>
                        <i data-lucide="lock" class="input-icon"></i>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="password-hint">يجب أن تحتوي على 8 أحرف على الأقل</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="أعد كتابة كلمة المرور" required>
                        <i data-lucide="lock" class="input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    <i data-lucide="user-plus"></i>
                    إنشاء الحساب
                </button>
            </form>

            <div class="auth-footer">
                <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>