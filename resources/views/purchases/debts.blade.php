@extends('layouts.app')

@section('title', 'ديون علينا - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>ديون علينا</h2>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon debts-on-us">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="summary-info">
            <h3>${{ number_format($totalUsd, 2) }}</h3>
            <p>إجمالي المستحق بالدولار</p>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon debts-on-us">
            <i data-lucide="banknote"></i>
        </div>
        <div class="summary-info">
            <h3>{{ number_format($totalSyp, 0) }} ليرة</h3>
            <p>إجمالي المستحق بالليرة</p>
        </div>
    </div>
</div>

<div class="debts-on-us-grid">
    @forelse($purchases as $purchase)
        <div class="debt-on-us-card">
            <div class="debt-header">
                <div class="debt-number">#{{ $purchase->purchase_number }}</div>
                <div class="debt-status debt">دين</div>
            </div>
            <div class="debt-info">
                <div class="debt-supplier">{{ $purchase->supplier }}</div>
                <div class="debt-details">{{ Str::limit($purchase->details, 80) }}</div>
                <div class="debt-footer">
                    <div class="debt-amount">
                        {{ number_format($purchase->amount, 2) }} {{ $purchase->currency == 'usd' ? 'دولار' : 'ليرة' }}
                    </div>
                    <div class="debt-date">{{ $purchase->purchase_date->format('Y-m-d') }}</div>
                </div>
                @if($purchase->attachments->count() > 0)
                    <div class="debt-attachments">
                        <i data-lucide="paperclip"></i>
                        <span>{{ $purchase->attachments->count() }} مرفقات</span>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="trending-down"></i>
            <h3>لا توجد ديون</h3>
            <p>جميع المشتريات مدفوعة نقداً</p>
            <a href="{{ route('purchases.create') }}" class="btn-primary">
                <i data-lucide="plus"></i>
                إضافة مشترى
            </a>
        </div>
    @endforelse
</div>
@endsection