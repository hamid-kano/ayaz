<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Error Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various error messages
    | that we need to display to the user.
    |
    */

    // HTTP Errors
    '404' => 'الصفحة غير موجودة',
    '403' => 'ممنوع الوصول',
    '500' => 'خطأ في الخادم',
    '503' => 'الخدمة غير متاحة',

    // Authentication Errors
    'login_failed' => 'فشل في تسجيل الدخول. يرجى التحقق من بيانات الاعتماد.',
    'account_disabled' => 'تم تعطيل حسابك. يرجى الاتصال بالإدارة.',
    'too_many_attempts' => 'محاولات كثيرة جداً. يرجى المحاولة مرة أخرى لاحقاً.',
    'session_expired' => 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مرة أخرى.',
    'unauthorized_access' => 'وصول غير مصرح به.',

    // Validation Errors
    'validation_failed' => 'فشل في التحقق من البيانات.',
    'invalid_input' => 'مدخل غير صالح.',
    'required_field_missing' => 'حقل مطلوب مفقود.',
    'invalid_email_format' => 'تنسيق البريد الإلكتروني غير صالح.',
    'password_too_short' => 'كلمة المرور قصيرة جداً.',
    'passwords_not_match' => 'كلمات المرور غير متطابقة.',

    // Database Errors
    'database_error' => 'خطأ في قاعدة البيانات.',
    'record_not_found' => 'السجل غير موجود.',
    'duplicate_entry' => 'إدخال مكرر.',
    'foreign_key_constraint' => 'لا يمكن حذف هذا السجل لأنه مرتبط بسجلات أخرى.',

    // File Upload Errors
    'file_too_large' => 'حجم الملف كبير جداً.',
    'invalid_file_type' => 'نوع الملف غير مدعوم.',
    'upload_failed' => 'فشل في رفع الملف.',
    'file_not_found' => 'الملف غير موجود.',

    // Permission Errors
    'access_denied' => 'تم رفض الوصول.',
    'insufficient_permissions' => 'صلاحيات غير كافية.',
    'admin_required' => 'يتطلب صلاحيات المدير.',

    // Network Errors
    'connection_failed' => 'فشل في الاتصال.',
    'timeout_error' => 'انتهت مهلة الاتصال.',
    'network_error' => 'خطأ في الشبكة.',

    // General Errors
    'something_went_wrong' => 'حدث خطأ ما.',
    'try_again_later' => 'يرجى المحاولة مرة أخرى لاحقاً.',
    'contact_support' => 'يرجى الاتصال بالدعم الفني.',
    'maintenance_mode' => 'الموقع في وضع الصيانة.',

    // Business Logic Errors
    'insufficient_balance' => 'الرصيد غير كافي.',
    'order_already_processed' => 'تم معالجة الطلب بالفعل.',
    'invalid_status_change' => 'تغيير الحالة غير صالح.',
    'deadline_passed' => 'انتهت المهلة المحددة.',

];