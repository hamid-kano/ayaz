@extends('layouts.app')

@section('title', 'ديون لنا - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>ديون لنا</h2>
</div>

<!-- Search Section -->
<div class="search-container">
    <form method="GET" action="{{ route('orders.debts') }}">
        <div class="search-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الديون لنا...">
            <button type="submit" class="search-btn">
                <i data-lucide="search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon debts-to-us">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="summary-info">
            <h3>${{ \App\Helpers\TranslationHelper::formatAmount($totalUsd) }}</h3>
            <p>إجمالي المتبقي بالدولار</p>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon debts-to-us">
            <i data-lucide="banknote"></i>
        </div>
        <div class="summary-info">
            <h3>{{ \App\Helpers\TranslationHelper::formatAmount($totalSyp) }} ليرة</h3>
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
                        <span class="debt-total">
                            @if($order->total_cost_syp > 0 && $order->total_cost_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @elseif($order->total_cost_syp > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س
                            @else
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @endif
                        </span>
                    </div>
                    <div class="debt-row">
                        <span class="debt-label">مدفوع:</span>
                        <span class="debt-paid">
                            @if($order->total_paid_syp > 0 && $order->total_paid_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_paid_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->total_paid_usd) }} $
                            @elseif($order->total_paid_syp > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_paid_syp) }} ل.س
                            @elseif($order->total_paid_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_paid_usd) }} $
                            @else
                                0
                            @endif
                        </span>
                    </div>
                </div>
                <div class="debt-remaining">
                    <span class="remaining-label">متبقي:</span>
                    <span class="remaining-amount">
                        @if($order->remaining_amount_syp > 0 && $order->remaining_amount_usd > 0)
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_usd) }} $
                        @elseif($order->remaining_amount_syp > 0)
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_syp) }} ل.س
                        @else
                            {{ \App\Helpers\TranslationHelper::formatAmount($order->remaining_amount_usd) }} $
                        @endif
                    </span>
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