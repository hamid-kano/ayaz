@extends('layouts.app')

@section('title', 'ديون لنا - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>ديون لنا</h2>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon debts-to-us">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="summary-info">
            <h3>${{ number_format($totalUsd, 2) }}</h3>
            <p>إجمالي المتبقي بالدولار</p>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon debts-to-us">
            <i data-lucide="banknote"></i>
        </div>
        <div class="summary-info">
            <h3>{{ number_format($totalSyp, 0) }} ليرة</h3>
            <p>إجمالي المتبقي بالليرة</p>
        </div>
    </div>
</div>

<div class="debts-grid">
    @forelse($orders as $order)
        <div class="debt-card">
            <div class="debt-header">
                <div class="debt-order">#{{ $order->order_number }}</div>
                <div class="debt-actions">
                    <a href="{{ route('receipts.create', ['order_id' => $order->id]) }}" class="add-receipt-btn" title="إضافة سند قبض">
                        <i data-lucide="plus"></i>
                    </a>
                    <a href="{{ route('orders.show', $order) }}" class="debt-link" title="عرض التفاصيل">
                        <i data-lucide="external-link"></i>
                    </a>
                </div>
            </div>
            <div class="debt-info">
                <div class="debt-customer">{{ $order->customer_name }}</div>
                <div class="debt-type">{{ $order->order_type }}</div>
                <div class="debt-details">
                    <div class="debt-row">
                        <span class="debt-label">إجمالي:</span>
                        <span class="debt-total">{{ number_format($order->cost, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
                    </div>
                    <div class="debt-row">
                        <span class="debt-label">مدفوع:</span>
                        <span class="debt-paid">{{ number_format($order->total_paid, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
                    </div>
                </div>
                <div class="debt-remaining">
                    <span class="remaining-label">متبقي:</span>
                    <span class="remaining-amount">{{ number_format($order->remaining_amount, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="trending-up"></i>
            <h3>لا توجد ديون</h3>
            <p>جميع الطلبيات مدفوعة بالكامل</p>
        </div>
    @endforelse
</div>
@endsection