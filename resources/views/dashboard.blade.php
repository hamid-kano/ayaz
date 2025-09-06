@extends('layouts.app')

@section('title', 'الرئيسية - مطبعة ريناس')

@section('content')
<div class="page home-page active">
    <!-- Menu Grid -->
    <div class="menu-grid">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('orders.create') }}" class="menu-item new-order">
                <div class="menu-icon">
                    <i data-lucide="plus-circle"></i>
                </div>
                <h3>طلبية جديدة</h3>
                <p>إضافة طلبية جديدة</p>
            </a>
        @endif

        <a href="{{ route('orders.index') }}" class="menu-item orders">
            <div class="menu-icon">
                <i data-lucide="file-text"></i>
            </div>
            <h3>الطلبات</h3>
            <p>{{ auth()->user()->isAdmin() ? 'عرض جميع الطلبات' : 'طلباتي' }}</p>
        </a>

        @if(auth()->user()->isAdmin())
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

            <a href="{{ route('users.index') }}" class="menu-item users-management">
                <div class="menu-icon">
                    <i data-lucide="users"></i>
                </div>
                <h3>إدارة المستخدمين</h3>
                <p>إضافة وتعديل المستخدمين</p>
            </a>
        @endif
    </div>
</div>
@endsection