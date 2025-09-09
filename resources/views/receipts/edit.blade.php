@extends('layouts.app')

@section('title', 'تعديل سند قبض - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('receipts.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>تعديل سند قبض</h2>
</div>

<form action="{{ route('receipts.update', $receipt) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label>رقم الطلبية</label>
        <input type="text" value="#{{ $receipt->order->order_number }} - {{ $receipt->order->customer_name }}" readonly class="readonly-input">
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" value="{{ old('amount', \App\Helpers\TranslationHelper::formatAmountForInput($receipt->amount)) }}" min="0" step="0.01" placeholder="أدخل المبلغ المقبوض" required>
            @error('amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>العملة</label>
            <input type="text" value="{{ $receipt->currency == 'usd' ? 'دولار أمريكي' : 'ليرة سورية' }}" readonly class="readonly-input">
        </div>
    </div>
    
    <div class="form-group">
        <label>التاريخ</label>
        <input type="date" name="receipt_date" value="{{ old('receipt_date', $receipt->receipt_date->format('Y-m-d')) }}" required>
        @error('receipt_date')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>ملاحظات (اختياري)</label>
        <textarea name="notes" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes', $receipt->notes) }}</textarea>
        @error('notes')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-actions">
        <a href="{{ route('receipts.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            تحديث السند
        </button>
    </div>
</form>

@endsection