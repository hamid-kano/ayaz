@extends('layouts.app')

@section('title', 'تعديل المشترى - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('purchases.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>تعديل المشترى</h2>
</div>

<form action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    @method('PUT')
    
    <!-- Purchase Info Section -->
    <div class="section-header">
        <i data-lucide="shopping-cart"></i>
        <h3>بيانات المشترى</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>رقم المشترى</label>
            <input type="text" value="{{ $purchase->purchase_number }}" readonly class="readonly-input">
        </div>
        <div class="form-group">
            <label>تاريخ المشترى</label>
            <input type="date" name="purchase_date" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
            @error('purchase_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" value="{{ old('amount', $purchase->amount) }}" min="0" step="0.01" required>
            @error('amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="currency" required>
                <option value="syp" {{ $purchase->currency == 'syp' ? 'selected' : '' }}>ليرة سورية</option>
                <option value="usd" {{ $purchase->currency == 'usd' ? 'selected' : '' }}>دولار أمريكي</option>
            </select>
            @error('currency')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-group">
        <label>حالة الدفع</label>
        <select name="status" required>
            <option value="cash" {{ $purchase->status == 'cash' ? 'selected' : '' }}>نقدي</option>
            <option value="debt" {{ $purchase->status == 'debt' ? 'selected' : '' }}>دين</option>
        </select>
        @error('status')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>اسم المورد</label>
        <input type="text" name="supplier" value="{{ old('supplier', $purchase->supplier) }}" required>
        @error('supplier')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>تفاصيل المشترى</label>
        <textarea name="details" rows="3" required>{{ old('details', $purchase->details) }}</textarea>
        @error('details')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ التعديلات
        </button>
    </div>
</form>
@endsection