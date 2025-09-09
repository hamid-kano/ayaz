/**
 * مساعدات JavaScript للتعامل مع المبالغ المالية
 */

// تنسيق المبلغ للعرض
function formatAmount(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '0';
    }
    
    const num = parseFloat(amount);
    
    // إذا كان الرقم صحيح (بدون كسور) أظهره بدون فاصلة
    if (Math.floor(num) === num) {
        return Math.floor(num).toLocaleString('en-US');
    }
    
    // إذا كان يحتوي على كسور أظهر رقمين بعد الفاصلة
    return num.toFixed(2).replace(/\.?0+$/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// تنسيق المبلغ لحقول الإدخال (دائماً رقمين بعد الفاصلة)
function formatAmountForInput(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '0.00';
    }
    return parseFloat(amount).toFixed(2);
}

// تحليل المبلغ من النص (إزالة التنسيق)
function parseAmount(amountString) {
    if (!amountString) return 0;
    return parseFloat(amountString.toString().replace(/,/g, '')) || 0;
}

// تطبيق التنسيق على جميع عناصر المبالغ في الصفحة
function initializeAmountFormatting() {
    // تنسيق عناصر العرض
    document.querySelectorAll('.amount-display').forEach(element => {
        const amount = parseAmount(element.textContent);
        element.textContent = formatAmount(amount);
        element.classList.add(Math.floor(amount) === amount ? 'integer-amount' : 'decimal-amount');
    });
    
    // تنسيق حقول الإدخال
    document.querySelectorAll('input[type="number"][step="0.01"]').forEach(input => {
        input.classList.add('amount-input');
        
        // تنسيق القيمة الحالية
        if (input.value) {
            input.value = formatAmountForInput(input.value);
        }
        
        // معالج التغيير
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = formatAmountForInput(this.value);
            }
        });
    });
}

// تشغيل التنسيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', initializeAmountFormatting);

// تصدير الدوال للاستخدام العام
window.formatAmount = formatAmount;
window.formatAmountForInput = formatAmountForInput;
window.parseAmount = parseAmount;
window.initializeAmountFormatting = initializeAmountFormatting;