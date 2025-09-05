<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - مطبعة ريناس</title>
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
                <p>تسجيل الدخول إلى حسابك</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="أدخل البريد الإلكتروني" required autofocus>
                        <i data-lucide="mail" class="input-icon"></i>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
                        <i data-lucide="lock" class="input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i data-lucide="eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        تذكرني
                    </label>
                </div>

                <button type="submit" class="auth-btn">
                    <i data-lucide="log-in"></i>
                    تسجيل الدخول
                </button>
            </form>

            @if (Route::has('register'))
                <div class="auth-footer">
                    <p>ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
                </div>
            @endif
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
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>