@extends('layouts.app')

@section('title', 'إضافة سند قبض - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('receipts.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>إضافة سند قبض</h2>
</div>

<form action="{{ route('receipts.store') }}" method="POST" class="form-container">
    @csrf
    
    <!-- Receipt Info Section -->
    <div class="section-header">
        <i data-lucide="banknote"></i>
        <h3>بيانات السند</h3>
    </div>
    
    <div class="form-group">
        <label>رقم الطلبية</label>
        <select name="order_id" required>
            <option value="">اختر رقم الطلبية</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                    {{ $order->order_number }} - {{ $order->customer_name }} 
                    (متبقي: {{ number_format($order->remaining_amount, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }})
                </option>
            @endforeach
        </select>
        @error('order_id')
            <span class="error-message">{{ $message }}</span>
        @enderror
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
        <label>التاريخ</label>
        <input type="date" name="receipt_date" value="{{ old('receipt_date', now()->format('Y-m-d')) }}" required>
        @error('receipt_date')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>ملاحظات (اختياري)</label>
        <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
        @error('notes')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('receipts.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ السند
        </button>
    </div>
</form>
@endsection