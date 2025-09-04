@extends('layouts.app')

@section('title', 'إضافة سند قبض - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('receipts.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>إضافة سند قبض</h2>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('receipts.store') }}" method="POST" class="form-container">
    @csrf
    
    <!-- Receipt Info Section -->
    <div class="section-header">
        <i data-lucide="banknote"></i>
        <h3>بيانات السند</h3>
    </div>
    
    <div class="form-group">
        <label>رقم الطلبية</label>
        <select name="order_id" required id="orderSelect">
            <option value="">اختر رقم الطلبية</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}" 
                        data-currency="{{ $order->currency }}"
                        data-remaining="{{ $order->remaining_amount }}"
                        {{ old('order_id') == $order->id ? 'selected' : '' }}>
                    #{{ $order->order_number }} - {{ $order->customer_name }} 
                    (متبقي: {{ number_format($order->remaining_amount, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }})
                </option>
            @endforeach
        </select>
        @error('order_id')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="selected-order-info" id="selectedOrderInfo" style="display: none;">
        <div class="order-summary">
            <h4>معلومات الطلبية</h4>
            <div class="summary-row">
                <span>المبلغ المتبقي:</span>
                <span id="remainingAmount" class="remaining-display"></span>
            </div>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" id="amountInput" value="{{ old('amount') }}" min="0" step="0.01" placeholder="أدخل المبلغ المقبوض" required>
            @error('amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <small class="form-hint">أدخل المبلغ المقبوض</small>
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="currency" id="currencySelect" required>
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
        <textarea name="notes" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('orderSelect');
    const currencySelect = document.getElementById('currencySelect');
    const amountInput = document.getElementById('amountInput');
    const selectedOrderInfo = document.getElementById('selectedOrderInfo');
    const remainingAmount = document.getElementById('remainingAmount');
    
    orderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const currency = selectedOption.dataset.currency;
            const remaining = selectedOption.dataset.remaining;
            
            // Update currency select
            currencySelect.value = currency;
            
            // Show order info
            selectedOrderInfo.style.display = 'block';
            remainingAmount.textContent = parseFloat(remaining).toFixed(2) + ' ' + (currency === 'usd' ? 'دولار' : 'ليرة');
            
            // Set max amount
            amountInput.max = remaining;
            amountInput.value = remaining;
        } else {
            selectedOrderInfo.style.display = 'none';
            amountInput.removeAttribute('max');
            amountInput.value = '';
        }
    });
    
    // Validate amount doesn't exceed remaining
    amountInput.addEventListener('input', function() {
        const selectedOption = orderSelect.options[orderSelect.selectedIndex];
        if (selectedOption.value) {
            const remaining = parseFloat(selectedOption.dataset.remaining);
            const amount = parseFloat(this.value);
            
            if (amount > remaining) {
                this.setCustomValidity('المبلغ لا يمكن أن يتجاوز المبلغ المتبقي');
            } else {
                this.setCustomValidity('');
            }
        }
    });
});
</script>
@endpush

@endsection