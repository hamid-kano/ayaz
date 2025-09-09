# تحديث معالجة المبالغ المالية

## التغييرات المطبقة

### 1. النماذج (Models)
- **OrderItem.php**: إضافة casting للسعر `'price' => 'decimal:2'`
- **Purchase.php**: يحتوي بالفعل على `'amount' => 'decimal:2'`
- **Receipt.php**: يحتوي بالفعل على `'amount' => 'decimal:2'`

### 2. المساعدات (Helpers)
- **TranslationHelper.php**: 
  - تحديث `formatAmount()` لإظهار الكسور عند الحاجة فقط
  - إضافة `formatAmountForInput()` لحقول الإدخال
  - إضافة `parseAmount()` لتحليل المبالغ

### 3. الكونترولرز (Controllers)
- **OrderController.php**: تغيير validation من `integer` إلى `numeric`
- **ReceiptController.php**: تغيير validation من `integer` إلى `numeric` مع `min:0.01`
- **PurchaseController.php**: يحتوي بالفعل على `numeric` validation

### 4. ملفات العرض (Views)
- **order-items.blade.php**: 
  - تحديث `step="0.01"` في حقل السعر
  - إضافة دالة JavaScript `formatAmount()`
  - تحديث عرض المبالغ في JavaScript
- **receipts/create.blade.php**: تحديث `step="0.01"` وتحسين JavaScript
- **receipts/edit.blade.php**: تحديث حقل المبلغ لاستخدام `formatAmountForInput()`
- **purchases/create.blade.php**: تحديث `step="0.01"`
- **purchases/edit.blade.php**: تحديث حقل المبلغ لاستخدام `formatAmountForInput()`

### 5. قاعدة البيانات
- **Migration**: إنشاء `2024_12_19_000001_update_amounts_precision.php` لتحديث دقة المبالغ

### 6. الملفات الإضافية
- **amounts.css**: تنسيق خاص بالمبالغ المالية
- **amounts.js**: دوال JavaScript للتعامل مع المبالغ
- **app.blade.php**: إضافة الملفات الجديدة

## كيفية تشغيل التحديثات

1. تشغيل المايكريشن:
```bash
php artisan migrate
```

2. مسح الكاش:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## السلوك الجديد

### الإدخال
- جميع حقول المبالغ تدعم الآن الكسور العشرية
- `step="0.01"` في جميع حقول الإدخال
- التحقق من صحة البيانات يقبل `numeric` بدلاً من `integer`

### العرض
- إذا كان المبلغ صحيح (مثل 1000): يظهر "1,000"
- إذا كان يحتوي كسور (مثل 1000.50): يظهر "1,000.50"
- إذا كان يحتوي كسور صفرية (مثل 1000.00): يظهر "1,000"

### قاعدة البيانات
- جميع حقول المبالغ تحفظ برقمين بعد الفاصلة
- `DECIMAL(10,2)` في جداول: `order_items.price`, `receipts.amount`, `purchases.amount`

## الملفات المتأثرة

### Models
- `app/Models/OrderItem.php`

### Helpers  
- `app/Helpers/TranslationHelper.php`

### Controllers
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/ReceiptController.php`

### Views
- `resources/views/components/order-items.blade.php`
- `resources/views/receipts/create.blade.php`
- `resources/views/receipts/edit.blade.php`
- `resources/views/purchases/create.blade.php`
- `resources/views/purchases/edit.blade.php`
- `resources/views/layouts/app.blade.php`

### Database
- `database/migrations/2024_12_19_000001_update_amounts_precision.php`

### Assets
- `public/css/amounts.css`
- `public/js/amounts.js`

## اختبار التحديثات

1. إنشاء طلبية جديدة بأسعار تحتوي كسور
2. إنشاء مقبوض بمبلغ يحتوي كسور
3. إنشاء مشترى بمبلغ يحتوي كسور
4. التأكد من العرض الصحيح في جميع الصفحات
5. التأكد من حفظ البيانات بالدقة الصحيحة