@extends('layouts.app')

@section('title', 'إضافة سند قبض - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('receipts.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>إضافة سند قبض</h2>
</div>



<form action="{{ route('receipts.store') }}" method="POST" class="form-container">
    @csrf
    <div class="form-group">
        <label>رقم الطلبية</label>
        <div class="search-select-container">
            <input type="text" id="orderSearch" placeholder="ابحث برقم الطلبية أو اسم الزبون..." class="search-input">
            <select name="order_id" required id="orderSelect">
                <option value="">اختر رقم الطلبية</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" 
                            data-remaining-syp="{{ $order->remaining_amount_syp }}"
                            data-remaining-usd="{{ $order->remaining_amount_usd }}"
                            data-search="{{ $order->order_number }} {{ $order->customer_name }}"
                            {{ (old('order_id') == $order->id || $selectedOrderId == $order->id) ? 'selected' : '' }}>
                        #{{ $order->order_number }} - {{ $order->customer_name }} 
                        (متبقي: 
                        @if($order->remaining_amount_syp > 0 && $order->remaining_amount_usd > 0)
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_usd) }} $
                        @elseif($order->remaining_amount_syp > 0)
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_syp) }} ل.س
                        @else
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_usd) }} $
                        @endif
                        )
                    </option>
                @endforeach
            </select>
        </div>
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
    const orderSearch = document.getElementById('orderSearch');
    const currencySelect = document.getElementById('currencySelect');
    const amountInput = document.getElementById('amountInput');
    const selectedOrderInfo = document.getElementById('selectedOrderInfo');
    const remainingAmount = document.getElementById('remainingAmount');
    
    // البحث في الطلبات
    orderSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = orderSelect.options;
        
        for (let i = 1; i < options.length; i++) {
            const option = options[i];
            const searchData = option.dataset.search.toLowerCase();
            
            if (searchData.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    // تفعيل الاختيار التلقائي عند تحميل الصفحة
    if (orderSelect.value) {
        orderSelect.dispatchEvent(new Event('change'));
    }
    
    orderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const remainingSyp = parseFloat(selectedOption.dataset.remainingSyp);
            const remainingUsd = parseFloat(selectedOption.dataset.remainingUsd);
            
            // Show order info
            selectedOrderInfo.style.display = 'block';
            
            // Display remaining amounts
            let remainingText = '';
            function formatAmount(amount) {
                if (Math.floor(amount) == amount) {
                    return Math.floor(amount).toLocaleString();
                }
                return parseFloat(amount).toFixed(2).replace(/\.?0+$/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            if (remainingSyp > 0 && remainingUsd > 0) {
                remainingText = formatAmount(remainingSyp) + ' ل.س + ' + formatAmount(remainingUsd) + ' دولار';
            } else if (remainingSyp > 0) {
                remainingText = formatAmount(remainingSyp) + ' ل.س';
                currencySelect.value = 'syp';
            } else if (remainingUsd > 0) {
                remainingText = formatAmount(remainingUsd) + ' دولار';
                currencySelect.value = 'usd';
            }
            
            remainingAmount.textContent = remainingText;
            
            // Enable currency select if both currencies have remaining amounts
            currencySelect.disabled = !(remainingSyp > 0 && remainingUsd > 0);
            
            // Update amount input based on selected currency
            updateAmountInput();
        } else {
            selectedOrderInfo.style.display = 'none';
            currencySelect.disabled = false;
            amountInput.removeAttribute('max');
            amountInput.value = '';
        }
    });
    
    // Add currency change handler
    currencySelect.addEventListener('change', updateAmountInput);
    
    function updateAmountInput() {
        const selectedOption = orderSelect.options[orderSelect.selectedIndex];
        if (selectedOption.value) {
            const currency = currencySelect.value;
            const remaining = currency === 'syp' ? 
                parseFloat(selectedOption.dataset.remainingSyp) : 
                parseFloat(selectedOption.dataset.remainingUsd);
            
            amountInput.max = remaining;
            amountInput.value = remaining > 0 ? remaining.toFixed(2) : '';
        }
    }
    
    // Validate amount doesn't exceed remaining
    amountInput.addEventListener('input', function() {
        const selectedOption = orderSelect.options[orderSelect.selectedIndex];
        if (selectedOption.value) {
            const currency = currencySelect.value;
            const remaining = currency === 'syp' ? 
                parseFloat(selectedOption.dataset.remainingSyp) : 
                parseFloat(selectedOption.dataset.remainingUsd);
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

<style>
.search-select-container {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 8px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

#orderSelect {
    max-height: 200px;
    overflow-y: auto;
}
</style>
@endpush

@endsection