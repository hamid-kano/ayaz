<script>
// Loading states for submit buttons
document.addEventListener('DOMContentLoaded', function() {
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
        if (form.method.toLowerCase() === 'post' && !action.includes('update')) {
            return 'جاري الإضافة...';
        }
        if (action.includes('update') || buttonText.includes('تعديل') || buttonText.includes('تحديث')) {
            return 'جاري التعديل...';
        }
        if (buttonText.includes('حفظ')) {
            return 'جاري الحفظ...';
        }
        if (buttonText.includes('إرسال')) {
            return 'جاري الإرسال...';
        }
        return 'جاري المعالجة...';
    }
});

// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Notifications toggle
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    if (notificationBtn && notificationsDropdown) {
        notificationBtn.addEventListener('click', function() {
            notificationsDropdown.classList.toggle('show');
            userDropdown?.classList.remove('show');
        });
    }
    
    // User menu toggle
    const userMenu = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function() {
            userDropdown.classList.toggle('show');
            notificationsDropdown?.classList.remove('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!userMenu?.contains(e.target)) {
            userDropdown?.classList.remove('show');
        }
        if (!notificationBtn?.contains(e.target)) {
            notificationsDropdown?.classList.remove('show');
        }
    });
});
</script>