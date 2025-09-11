@extends('layouts.app')

@section('title', 'إضافة مشترى - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('purchases.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>إضافة مشترى</h2>
</div>



<form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    
    <!-- Purchase Info Section -->
    <div class="section-header">
        <i data-lucide="shopping-cart"></i>
        <h3>بيانات المشترى</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>رقم المشترى</label>
            <input type="text" value="سيتم إنشاؤه تلقائياً" readonly class="readonly-input">
        </div>
        <div class="form-group">
            <label>تاريخ المشترى</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
            @error('purchase_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.000001" placeholder="أدخل المبلغ" required>
            @error('amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="currency" required>
                <option value="syp" {{ old('currency') == 'syp' ? 'selected' : '' }}>ليرة سورية</option>
                <option value="usd" {{ old('currency') == 'usd' ? 'selected' : '' }}>دولار أمريكي</option>
            </select>
            @error('currency')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-group">
        <label>حالة الدفع</label>
        <select name="status" required id="statusSelect">
            <option value="">اختر حالة الدفع</option>
            <option value="cash" {{ old('status') == 'cash' ? 'selected' : '' }}>نقدي</option>
            <option value="debt" {{ old('status') == 'debt' ? 'selected' : '' }}>دين</option>
        </select>
        @error('status')
            <span class="error-message">{{ $message }}</span>
        @enderror
        <small class="form-hint">اختر نقدي إذا تم الدفع فوراً أو دين إذا كان مؤجلاً</small>
    </div>
    
    <div class="form-group">
        <label>اسم المورد</label>
        <input type="text" name="supplier" value="{{ old('supplier') }}" placeholder="أدخل اسم المورد" required>
        @error('supplier')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>تفاصيل المشترى</label>
        <textarea name="details" rows="3" placeholder="وصف مفصل للمشترى..." required>{{ old('details') }}</textarea>
        @error('details')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    <div class="form-group">
        <label>رفع ملفات</label>
        <div class="file-upload-area">
            <input type="file" class="file-upload-input" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
            <div class="file-upload-content">
                <div class="file-upload-icon">
                    <i data-lucide="upload"></i>
                </div>
                <div class="file-upload-text">
                    <h4>اسحب الملفات هنا أو اضغط للتحديد</h4>
                    <p>PDF وصور</p>
                </div>
            </div>
        </div>
        <div class="uploaded-files"></div>
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ المشترى
        </button>
    </div>
</form>
@endsection