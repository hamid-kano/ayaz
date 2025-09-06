# إعداد Cron Job للتذكيرات

## للهوستينغر (Hostinger)

### الطريقة الأولى: استخدام ملف cron.php
```bash
*/5 * * * * /usr/bin/php /home/username/public_html/cron.php
```

### الطريقة الثانية: استخدام artisan مباشرة
```bash
*/5 * * * * cd /home/username/public_html && php artisan schedule:run
```

### الطريقة الثالثة: تشغيل الأمر مباشرة
```bash
0 * * * * cd /home/username/public_html && php artisan orders:check-reminders
```

## تعليمات الإعداد

1. **تسجيل الدخول إلى لوحة تحكم الهوستينغر**
2. **الذهاب إلى Advanced → Cron Jobs**
3. **إضافة cron job جديد**
4. **اختيار التوقيت المناسب:**
   - كل 5 دقائق: `*/5 * * * *`
   - كل ساعة: `0 * * * *`
   - كل يوم في الساعة 9 صباحاً: `0 9 * * *`

5. **إدخال الأمر المناسب من الأعلى**

## ملاحظات مهمة

- استبدل `/home/username/public_html/` بالمسار الفعلي لمشروعك
- تأكد من أن PHP متاح في المسار `/usr/bin/php`
- يمكنك اختبار الأمر يدوياً أولاً: `php artisan orders:check-reminders`
- تأكد من تفعيل الإشعارات في إعدادات النظام

## اختبار الأمر

```bash
# تشغيل الأمر يدوياً للاختبار
php artisan orders:check-reminders

# عرض جميع الأوامر المتاحة
php artisan list

# عرض جدولة المهام
php artisan schedule:list
```