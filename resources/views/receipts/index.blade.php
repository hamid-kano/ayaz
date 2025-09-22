@extends('layouts.app')

@section('title', 'المقبوضات - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>المقبوضات</h2>
    @if (auth()->user()->canEditOrders())
        <a href="{{ route('receipts.create') }}" class="add-btn">
            <i data-lucide="plus"></i>
        </a>
    @else
        <div></div>
    @endif
</div>

<!-- Search Section -->
<div class="search-container">
    <form method="GET" action="{{ route('receipts.index') }}">
        <div class="search-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في المقبوضات...">
            <button type="submit" class="search-btn">
                <i data-lucide="search"></i>
            </button>
        </div>
    </form>
</div>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h3>${{ \App\Helpers\TranslationHelper::formatAmount($receipts->where('currency', 'usd')->sum('amount')) }}</h3>
            <p>إجمالي المقبوضات بالدولار</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
            <i data-lucide="banknote"></i>
        </div>
        <div class="stat-info">
            <h3>{{ \App\Helpers\TranslationHelper::formatAmount($receipts->where('currency', 'syp')->sum('amount')) }} ليرة</h3>
            <p>إجمالي المقبوضات بالليرة</p>
        </div>
    </div>
</div>

@if($receipts->count() > 0)
<div class="receipts-grid">
    @foreach($receipts as $receipt)
        <div class="receipt-card">
            <div class="receipt-header">
                <div class="receipt-order">#{{ $receipt->order->order_number }}</div>
                <a href="{{ route('orders.show', $receipt->order) }}" class="receipt-link" title="عرض الطلبية">
                    <i data-lucide="external-link"></i>
                </a>
            </div>
            <div class="receipt-body">
                <div class="receipt-customer">{{ $receipt->order->customer_name }}</div>
                @if($receipt->notes)
                    <div class="receipt-notes">{{ Str::limit($receipt->notes, 50) }}</div>
                @endif
            </div>
            <div class="receipt-footer">
                <div class="receipt-amount">
                    {{ \App\Helpers\TranslationHelper::formatAmount($receipt->amount) }} {{ $receipt->currency == 'usd' ? 'دولار' : 'ليرة' }}
                </div>
                <div class="receipt-date">{{ \App\Helpers\TranslationHelper::formatDate($receipt->receipt_date) }}</div>
            </div>
            <div class="receipt-actions">
                @if (auth()->user()->canEditOrders())
                    <a href="{{ route('receipts.edit', $receipt) }}" class="action-btn edit" title="تعديل">
                        <i data-lucide="edit-2"></i>
                    </a>
                @endif
                @if (auth()->user()->canDeleteOrders())
                    <form method="POST" action="{{ route('receipts.destroy', $receipt) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="action-btn delete" title="حذف" 
                            onclick="showDeleteModal('{{ route('receipts.destroy', $receipt) }}', 'سند القبض #{{ $receipt->id }}', this.closest('form'))">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
@else
<div class="empty-state-container">
    <div class="empty-state">
        <div class="empty-icon">
            <i data-lucide="banknote"></i>
        </div>
        <h3>لا توجد مقبوضات</h3>
        <p>ابدأ بإضافة سند قبض جديد لتتبع المدفوعات</p>
        <a href="{{ route('receipts.create', request()->has('order_id') ? ['order_id' => request('order_id')] : []) }}" class="btn-primary">
            <i data-lucide="plus"></i>
            إضافة سند قبض
        </a>
    </div>
</div>
@endif

@include('components.delete-modal')
@include('components.delete-modal-script')

@endsection