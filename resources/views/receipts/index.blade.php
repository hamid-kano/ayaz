@extends('layouts.app')

@section('title', 'المقبوضات - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>المقبوضات</h2>
    <a href="{{ route('receipts.create') }}" class="add-btn">
        <i data-lucide="plus"></i>
    </a>
</div>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-icon">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="stat-info">
            <h3>${{ number_format($receipts->where('currency', 'usd')->sum('amount'), 2) }}</h3>
            <p>إجمالي المقبوضات بالدولار</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i data-lucide="banknote"></i>
        </div>
        <div class="stat-info">
            <h3>{{ number_format($receipts->where('currency', 'syp')->sum('amount'), 0) }} ليرة</h3>
            <p>إجمالي المقبوضات بالليرة</p>
        </div>
    </div>
</div>

<div class="receipts-grid">
    @forelse($receipts as $receipt)
        <div class="receipt-card">
            <div class="receipt-header">
                <div class="receipt-order">#{{ $receipt->order->order_number }}</div>
                <a href="{{ route('orders.show', $receipt->order) }}" class="receipt-link">
                    <i data-lucide="external-link"></i>
                </a>
            </div>
            <div class="receipt-info">
                <div class="receipt-amount">
                    {{ number_format($receipt->amount, 2) }} {{ $receipt->currency == 'usd' ? 'دولار' : 'ليرة' }}
                </div>
                <div class="receipt-date">{{ $receipt->receipt_date->format('Y-m-d') }}</div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="banknote"></i>
            <h3>لا توجد مقبوضات</h3>
            <p>ابدأ بإضافة سند قبض جديد</p>
            <a href="{{ route('receipts.create') }}" class="btn-primary">
                <i data-lucide="plus"></i>
                إضافة سند قبض
            </a>
        </div>
    @endforelse
</div>
@endsection