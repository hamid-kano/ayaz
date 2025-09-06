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

<!-- Search Section -->
<div class="search-container">
    <form method="GET" action="{{ route('purchases.index') }}">
        <div class="search-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في المشتريات...">
            <button type="submit" class="search-btn">
                <i data-lucide="search"></i>
            </button>
        </div>
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
    </form>
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
            <div class="purchase-footer">
                @if($purchase->attachments->count() > 0)
                    <div class="attachment-count">
                        <i data-lucide="paperclip"></i>
                        <span>{{ $purchase->attachments->count() }} مرفقات</span>
                    </div>
                @endif
                <div class="purchase-actions">
                    <a href="{{ route('purchases.edit', $purchase) }}" class="action-btn edit" title="تعديل">
                        <i data-lucide="edit-2"></i>
                    </a>
                    <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="action-btn delete" title="حذف" 
                            onclick="showDeleteModal('{{ route('purchases.destroy', $purchase) }}', 'المشترى #{{ $purchase->purchase_number }}', this.closest('form'))">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                </div>
            </div>
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

@include('components.delete-modal')
@include('components.delete-modal-script')

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