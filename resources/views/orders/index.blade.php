@extends('layouts.app')

@section('title', 'الطلبات - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>الطلبات</h2>
    <a href="{{ route('orders.create') }}" class="add-btn">
        <i data-lucide="plus"></i>
    </a>
</div>



<!-- Search Section -->
<div class="search-container">
    <form method="GET" action="{{ route('orders.index') }}">
        <div class="search-group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الطلبات...">
            <button type="submit" class="search-btn">
                <i data-lucide="search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Orders Grid -->
<div class="orders-grid">
    @forelse($orders as $order)
        <div class="order-card" data-order="{{ $order->order_number }}" data-customer="{{ $order->customer_name }}" data-type="{{ $order->order_type }}">
            <div class="order-header">
                <div class="order-number">#{{ $order->order_number }}</div>
                <div class="order-status {{ $order->status }}">
                    @switch($order->status)
                        @case('new') جديدة @break
                        @case('in-progress') قيد التنفيذ @break
                        @case('delivered') تم التسليم @break
                        @case('cancelled') ملغاة @break
                    @endswitch
                </div>
            </div>
            <div class="order-info">
                <h4>{{ $order->customer_name }}</h4>
                <p class="order-type">{{ $order->order_type }}</p>
                <p class="order-details">{{ Str::limit($order->order_details, 50) }}</p>
            </div>
            <div class="order-footer">
                <div class="order-meta">
                    <div class="order-cost">
                        {{ number_format($order->cost, 2) }} 
                        {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}
                    </div>
                    <div class="order-date">{{ $order->order_date->format('Y-m-d') }}</div>
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.show', $order) }}" class="action-btn details" title="عرض التفاصيل">
                        <i data-lucide="eye"></i>
                    </a>
                    <a href="{{ route('orders.edit', $order) }}" class="action-btn edit" title="تعديل">
                        <i data-lucide="edit-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="file-text"></i>
            <h3>لا توجد طلبيات</h3>
            <p>ابدأ بإضافة طلبية جديدة</p>
            <a href="{{ route('orders.create') }}" class="btn-primary">
                <i data-lucide="plus"></i>
                إضافة طلبية
            </a>
        </div>
    @endforelse
</div>
@endsection