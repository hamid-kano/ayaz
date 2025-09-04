@extends('layouts.app')

@section('title', 'الرئيسية - مطبعة ريناس')

@section('content')
<div class="page home-page active">
    <!-- Menu Grid -->
    <div class="menu-grid">
        <a href="{{ route('orders.create') }}" class="menu-item new-order">
            <div class="menu-icon">
                <i data-lucide="plus-circle"></i>
            </div>
            <h3>طلبية جديدة</h3>
            <p>إضافة طلبية جديدة</p>
        </a>

        <a href="{{ route('orders.index') }}" class="menu-item orders">
            <div class="menu-icon">
                <i data-lucide="file-text"></i>
            </div>
            <h3>الطلبات</h3>
            <p>عرض جميع الطلبات</p>
        </a>

        <a href="{{ route('orders.debts') }}" class="menu-item debts-to-us">
            <div class="menu-icon">
                <i data-lucide="trending-up"></i>
            </div>
            <h3>ديون لنا</h3>
            <p>المبالغ المتبقية</p>
        </a>

        <a href="{{ route('receipts.index') }}" class="menu-item receipts">
            <div class="menu-icon">
                <i data-lucide="banknote"></i>
            </div>
            <h3>مقبوضات</h3>
            <p>المبالغ المحصلة</p>
        </a>

        <a href="{{ route('purchases.index') }}" class="menu-item purchases">
            <div class="menu-icon">
                <i data-lucide="shopping-cart"></i>
            </div>
            <h3>مشتريات</h3>
            <p>إدارة المشتريات</p>
        </a>

        <a href="{{ route('purchases.debts') }}" class="menu-item debts-on-us">
            <div class="menu-icon">
                <i data-lucide="trending-down"></i>
            </div>
            <h3>ديون علينا</h3>
            <p>المبالغ المستحقة علينا</p>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon new-order">
                <i data-lucide="plus-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['new_orders'] }}</h3>
                <p>طلبيات جديدة</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon receipts">
                <i data-lucide="banknote"></i>
            </div>
            <div class="stat-info">
                <h3>${{ number_format($stats['total_receipts_usd'], 2) }}</h3>
                <p>إجمالي المقبوضات</p>
            </div>
        </div>
    </div>
</div>
@endsection