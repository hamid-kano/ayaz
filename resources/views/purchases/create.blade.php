@extends('layouts.app')

@section('title', 'إضافة مشترى - مطبعة آياز')

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
            <label>الرقم</label>
            <input type="text" value="سيتم إنشاؤه تلقائياً" readonly>
        </div>
        <div class="form-group">
            <label>التاريخ</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
            @error('purchase_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required>
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
        <label>الحالة</label>
        <select name="status" required>
            <option value="cash" {{ old('status') == 'cash' ? 'selected' : '' }}>نقدي</option>
            <option value="debt" {{ old('status') == 'debt' ? 'selected' : '' }}>دين</option>
        </select>
        @error('status')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>المورد</label>
        <input type="text" name="supplier" value="{{ old('supplier') }}" required>
        @error('supplier')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>التفاصيل</label>
        <textarea name="details" rows="3" required>{{ old('details') }}</textarea>
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