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
            <p>{{ auth()->user()->isAdmin() || auth()->user()->isAuditor() ? 'عرض جميع الطلبات' : 'طلباتي' }}</p>
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
        @endif

        @if(auth()->user()->isAdmin())

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

            <a href="{{ route('orders.archives') }}" class="menu-item archives">
                <div class="menu-icon">
                    <i data-lucide="archive"></i>
                </div>
                <h3>أرشيف الطلبيات</h3>
                <p>الطلبيات المؤرشفة</p>
            </a>

            <a href="{{ route('users.index') }}" class="menu-item users-management">
                <div class="menu-icon">
                    <i data-lucide="users"></i>
                </div>
                <h3>إدارة المستخدمين</h3>
                <p>إضافة وتعديل المستخدمين</p>
            </a>

            <form method="POST" action="{{ route('reports.send-telegram') }}" style="display: contents;">
                @csrf
                <button type="submit" class="menu-item telegram-report" style="border: none; cursor: pointer; font-family: inherit; text-decoration: none; color: inherit; background: white;">
                    <div class="menu-icon">
                        <i data-lucide="send"></i>
                    </div>
                    <h3>إرسال التقرير</h3>
                    <p>إرسال التقرير لتلغرام</p>
                </button>
            </form>
        @endif
    </div>

    <!-- Floating Refresh Button -->
    <button class="floating-refresh-btn" onclick="window.location.reload()" title="إعادة تحميل">
        <i data-lucide="refresh-cw"></i>
    </button>
</div>
@endsection