@extends('layouts.app')

@section('title', 'طلبية جديدة - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('orders.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>طلبية جديدة</h2>
</div>

<form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    
    <!-- Order Info Section -->
    <div class="section-header">
        <i data-lucide="file-text"></i>
        <h3>بيانات الطلبية</h3>
    </div>
    
    <div class="form-group">
        <label>اسم العميل</label>
        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required>
        @error('customer_name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>النوع</label>
        <select name="order_type" required>
            <option value="">اختر نوع الطلبية</option>
            <option value="business-cards" {{ old('order_type') == 'business-cards' ? 'selected' : '' }}>بطاقات عمل</option>
            <option value="flyers" {{ old('order_type') == 'flyers' ? 'selected' : '' }}>فلايرز</option>
            <option value="brochures" {{ old('order_type') == 'brochures' ? 'selected' : '' }}>بروشورات</option>
            <option value="banners" {{ old('order_type') == 'banners' ? 'selected' : '' }}>لافتات</option>
            <option value="books" {{ old('order_type') == 'books' ? 'selected' : '' }}>كتب ومجلات</option>
            <option value="other" {{ old('order_type') == 'other' ? 'selected' : '' }}>أخرى</option>
        </select>
        @error('order_type')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>التفاصيل</label>
        <textarea name="order_details" rows="3" required>{{ old('order_details') }}</textarea>
        @error('order_details')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>الكلفة</label>
            <input type="number" name="cost" value="{{ old('cost') }}" min="0" step="0.01" required>
            @error('cost')
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
        <label>تاريخ التسليم</label>
        <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" required>
        @error('delivery_date')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <!-- Users Section -->
    <div class="section-header">
        <i data-lucide="users"></i>
        <h3>المستخدمين</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>مدقق الطلب</label>
            <select name="reviewer_id">
                <option value="">اختر مدقق</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('reviewer_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>المنفذ للطلبية</label>
            <select name="executor_id">
                <option value="">اختر منفذ</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('executor_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    <div class="form-group">
        <label>رفع ملفات</label>
        <div class="file-upload-area">
            <input type="file" class="file-upload-input" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.psd">
            <div class="file-upload-content">
                <div class="file-upload-icon">
                    <i data-lucide="upload"></i>
                </div>
                <div class="file-upload-text">
                    <h4>اسحب الملفات هنا أو اضغط للتحديد</h4>
                    <p>PDF, Word, صور, Excel, Photoshop</p>
                </div>
            </div>
        </div>
        <div class="uploaded-files"></div>
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('orders.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ الطلبية
        </button>
    </div>
</form>
@endsection