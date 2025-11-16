@extends('layouts.app')

@section('title', 'طلبية جديدة - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('orders.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>طلبية جديدة</h2>
</div>



<form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    <div class="form-row">
        <div class="form-group">
            <label>اسم الزبون</label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="أدخل اسم الزبون" required>
            @error('customer_name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label>رقم هاتف الزبون</label>
            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="اختياري">
            @error('customer_phone')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-group">
        <label>النوع</label>
        <input type="text" name="order_type" value="{{ old('order_type') }}" placeholder="أدخل نوع الطلبية" required>
        @error('order_type')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>ملاحظات</label>
        <textarea name="order_details" rows="3" placeholder="ملاحظات عن الطلبية..." required>{{ old('order_details') }}</textarea>
        @error('order_details')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    

    
    <div class="form-row">
        <div class="form-group">
            <label>تاريخ ووقت التسليم</label>
            <input type="datetime-local" name="delivery_date" value="{{ old('delivery_date') }}" required>
            @error('delivery_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>نوع الطلبية</label>
            <select name="is_urgent">
                <option value="0" {{ old('is_urgent') == '0' ? 'selected' : '' }}>عادية</option>
                <option value="1" {{ old('is_urgent') == '1' ? 'selected' : '' }}>مستعجلة</option>
            </select>
            @error('is_urgent')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <!-- Users Section -->
    <div class="section-header">
        <i data-lucide="users"></i>
        <h3>المستخدمين</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>مدقق الطلب</label>
            <input type="text" name="reviewer_name" value="{{ old('reviewer_name') }}" placeholder="أدخل اسم المدقق">
            @error('reviewer_name')
                <span class="error-message">{{ $message }}</span>
            @enderror
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
    
    <!-- Order Items Section -->
    @include('components.order-items', ['items' => collect([]), 'editable' => true])
    
    <!-- Receipt Section -->
    <div class="section-header">
        <i data-lucide="banknote"></i>
        <h3>سند قبض (سلفة)</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="receipt_amount" value="{{ old('receipt_amount') }}" min="0" step="0.000001" placeholder="أدخل مبلغ السلفة">
            @error('receipt_amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <small class="form-hint">اختياري - اترك فارغاً إذا لم يتم دفع سلفة</small>
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="receipt_currency">
                <option value="syp" {{ old('receipt_currency') == 'syp' ? 'selected' : '' }}>ليرة سورية</option>
                <option value="usd" {{ old('receipt_currency') == 'usd' ? 'selected' : '' }}>دولار أمريكي</option>
            </select>
            @error('receipt_currency')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>تاريخ القبض</label>
            <input type="date" name="receipt_date" value="{{ old('receipt_date', now()->format('Y-m-d')) }}">
            @error('receipt_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>ملاحظات القبض (اختياري)</label>
            <textarea name="receipt_notes" rows="2" placeholder="أي ملاحظات إضافية...">{{ old('receipt_notes') }}</textarea>
            @error('receipt_notes')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    <div class="form-group" x-data="{ 
        files: [],
        getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (['pdf'].includes(ext)) return 'fas fa-file-pdf';
            if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'fas fa-file-image';
            if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel';
            if (['psd'].includes(ext)) return 'fas fa-palette';
            return 'fas fa-file';
        },
        getFileType(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (['pdf'].includes(ext)) return 'pdf';
            if (['doc', 'docx'].includes(ext)) return 'word';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image';
            if (['xls', 'xlsx'].includes(ext)) return 'excel';
            if (['psd'].includes(ext)) return 'photoshop';
            return 'default';
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }
    }">
        <label>رفع ملفات</label>
        <div class="file-upload-area">
            <input type="file" class="file-upload-input" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.psd" @change="files = Array.from($event.target.files)">
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
        <div x-show="files.length > 0" class="selected-files">
            <div class="files-header">
                <i class="fas fa-folder"></i>
                <h5>الملفات المختارة</h5>
                <span class="files-count" x-text="files.length"></span>
            </div>
            <div class="files-grid">
                <template x-for="(file, index) in files" :key="index">
                    <div class="file-card">
                        <div class="file-icon" :class="getFileType(file.name)">
                            <i :class="getFileIcon(file.name)"></i>
                        </div>
                        <div class="file-info">
                            <span class="file-name" x-text="file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name"></span>
                            <small class="file-size" x-text="formatFileSize(file.size)"></small>
                        </div>
                    </div>
                </template>
            </div>
        </div>
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