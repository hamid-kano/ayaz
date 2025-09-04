@extends('layouts.app')

@section('title', 'تعديل الطلبية - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('orders.show', $order) }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>تعديل الطلبية</h2>
</div>

<form action="{{ route('orders.update', $order) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')
    <div class="form-row">
        <div class="form-group">
            <label>رقم الطلبية</label>
            <input type="text" value="{{ $order->order_number }}" readonly>
        </div>
        <div class="form-group">
            <label>التاريخ</label>
            <input type="date" name="order_date" value="{{ $order->order_date->format('Y-m-d') }}" required>
        </div>
    </div>
    
    <div class="form-group">
        <label>اسم العميل</label>
        <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
        @error('customer_name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>النوع</label>
        <select name="order_type" required>
            <option value="business-cards" {{ $order->order_type == 'business-cards' ? 'selected' : '' }}>بطاقات عمل</option>
            <option value="flyers" {{ $order->order_type == 'flyers' ? 'selected' : '' }}>فلايرز</option>
            <option value="brochures" {{ $order->order_type == 'brochures' ? 'selected' : '' }}>بروشورات</option>
            <option value="banners" {{ $order->order_type == 'banners' ? 'selected' : '' }}>لافتات</option>
            <option value="books" {{ $order->order_type == 'books' ? 'selected' : '' }}>كتب ومجلات</option>
            <option value="other" {{ $order->order_type == 'other' ? 'selected' : '' }}>أخرى</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>التفاصيل</label>
        <textarea name="order_details" rows="3" required>{{ old('order_details', $order->order_details) }}</textarea>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>الكلفة</label>
            <input type="number" name="cost" value="{{ old('cost', $order->cost) }}" min="0" step="0.01" required>
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="currency" required>
                <option value="syp" {{ $order->currency == 'syp' ? 'selected' : '' }}>ليرة سورية</option>
                <option value="usd" {{ $order->currency == 'usd' ? 'selected' : '' }}>دولار أمريكي</option>
            </select>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>حالة الطلبية</label>
            <select name="status" required>
                <option value="new" {{ $order->status == 'new' ? 'selected' : '' }}>جديدة</option>
                <option value="in-progress" {{ $order->status == 'in-progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
            </select>
        </div>
        <div class="form-group">
            <label>تاريخ التسليم</label>
            <input type="date" name="delivery_date" value="{{ $order->delivery_date->format('Y-m-d') }}" required>
        </div>
    </div>
    
    <!-- Users Section -->
    <div class="section-header">
        <i data-lucide="users"></i>
        <h3>المستخدمين</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>مدقق الطلب</label>
            <select name="reviewer_id">
                <option value="">اختر مدقق</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $order->reviewer_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>المنفذ للطلبية</label>
            <select name="executor_id">
                <option value="">اختر منفذ</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $order->executor_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('orders.show', $order) }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ التعديلات
        </button>
    </div>
</form>
@endsection