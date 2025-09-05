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
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل الاسم الكامل" required autofocus>
                        <i data-lucide="user" class="input-icon"></i>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="أدخل البريد الإلكتروني" required>
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
            
            // Loading states for submit buttons
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML || submitBtn.value;
                        const loadingText = getLoadingText(form, submitBtn);
                        
                        submitBtn.disabled = true;
                        if (submitBtn.innerHTML !== undefined) {
                            submitBtn.innerHTML = `<i data-lucide="loader-2" class="animate-spin"></i> ${loadingText}`;
                            lucide.createIcons();
                        } else {
                            submitBtn.value = loadingText;
                        }
                        
                        // Re-enable after 10 seconds as fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            if (submitBtn.innerHTML !== undefined) {
                                submitBtn.innerHTML = originalText;
                                lucide.createIcons();
                            } else {
                                submitBtn.value = originalText;
                            }
                        }, 10000);
                    }
                });
            });
            
            function getLoadingText(form, button) {
                const action = form.action.toLowerCase();
                const buttonText = (button.innerHTML || button.value).toLowerCase();
                
                if (action.includes('login') || buttonText.includes('دخول')) {
                    return 'جاري تسجيل الدخول...';
                }
                if (action.includes('register') || buttonText.includes('إنشاء') || buttonText.includes('تسجيل')) {
                    return 'جاري التسجيل...';
                }
                return 'جاري المعالجة...';
            }
        });
        
        // Add CSS for loading states
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            button:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>