# تشغيل السيدرز

## الأوامر المطلوبة:

```bash
# تشغيل المايجريشن
php artisan migrate

# تشغيل السيدرز
php artisan db:seed

# أو تشغيل سيدر محدد
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=OrderSeeder
php artisan db:seed --class=ReceiptSeeder
php artisan db:seed --class=PurchaseSeeder
php artisan db:seed --class=AttachmentSeeder
php artisan db:seed --class=AudioRecordingSeeder

# إعادة تشغيل المايجريشن مع السيدرز
php artisan migrate:fresh --seed
```

## البيانات المولدة:

### المستخدمين (3):
- أحمد محمد (admin@ayaz.com)
- فاطمة علي (fatima@ayaz.com)  
- عمر خالد (omar@ayaz.com)
- كلمة المرور: password

### الطلبات (6):
- ORD-001: بطاقات عمل (250 دولار)
- ORD-002: فلايرز (150,000 ليرة)
- ORD-003: بروشورات (180 دولار)
- ORD-004: لافتات (300 دولار)
- ORD-005: كتب (120,000 ليرة)
- ORD-006: بطاقات عمل فاخرة (200 دولار)

### المقبوضات (4):
- دفعات جزئية وكاملة للطلبات

### المشتريات (6):
- مواد طباعة وأحبار ومعدات
- حالات نقدي ودين

### المرفقات (5):
- ملفات PDF وصور ومستندات

### التسجيلات الصوتية (4):
- تسجيلات لبعض الطلبات