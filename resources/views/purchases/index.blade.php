@extends('layouts.app')

@section('title', 'المشتريات - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>المشتريات</h2>
    <a href="{{ route('purchases.create') }}" class="add-btn">
        <i data-lucide="plus"></i>
    </a>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <button class="tab-btn {{ request('status') == '' ? 'active' : '' }}" onclick="filterPurchases('')">الكل</button>
    <button class="tab-btn {{ request('status') == 'cash' ? 'active' : '' }}" onclick="filterPurchases('cash')">نقدي</button>
    <button class="tab-btn {{ request('status') == 'debt' ? 'active' : '' }}" onclick="filterPurchases('debt')">دين</button>
</div>

<div class="purchases-grid">
    @forelse($purchases as $purchase)
        <div class="purchase-card">
            <div class="purchase-header">
                <div class="purchase-number">#{{ $purchase->purchase_number }}</div>
                <div class="purchase-status {{ $purchase->status }}">
                    {{ $purchase->status == 'cash' ? 'نقدي' : 'دين' }}
                </div>
            </div>
            <div class="purchase-info">
                <div class="purchase-supplier">{{ $purchase->supplier }}</div>
                <div class="purchase-details">{{ Str::limit($purchase->details, 60) }}</div>
                <div class="purchase-meta">
                    <div class="purchase-amount">
                        {{ number_format($purchase->amount, 2) }} {{ $purchase->currency == 'usd' ? 'دولار' : 'ليرة' }}
                    </div>
                    <div class="purchase-date">{{ $purchase->purchase_date->format('Y-m-d') }}</div>
                </div>
            </div>
            @if($purchase->attachments->count() > 0)
                <div class="purchase-attachments">
                    <div class="attachment-count">
                        <i data-lucide="paperclip"></i>
                        <span>{{ $purchase->attachments->count() }} مرفقات</span>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="shopping-cart"></i>
            <h3>لا توجد مشتريات</h3>
            <p>ابدأ بإضافة مشترى جديد</p>
            <a href="{{ route('purchases.create') }}" class="btn-primary">
                <i data-lucide="plus"></i>
                إضافة مشترى
            </a>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
function filterPurchases(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location = url;
}
</script>
@endpush
@endsection