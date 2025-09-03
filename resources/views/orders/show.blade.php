@extends('layouts.app')

@section('title', 'تفاصيل الطلبية - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('orders.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>تفاصيل الطلبية</h2>
    <a href="{{ route('orders.edit', $order) }}" class="add-btn">
        <i data-lucide="edit-2"></i>
    </a>
</div>

<div class="view-container">
    <!-- Order Info Section -->
    <div class="section-header">
        <i data-lucide="file-text"></i>
        <h3>بيانات الطلبية</h3>
    </div>
    
    <div class="info-grid">
        <div class="info-item">
            <label>رقم الطلبية</label>
            <span>{{ $order->order_number }}</span>
        </div>
        <div class="info-item">
            <label>التاريخ</label>
            <span>{{ $order->order_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-item">
            <label>اسم العميل</label>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="info-item">
            <label>النوع</label>
            <span>{{ $order->order_type }}</span>
        </div>
        <div class="info-item full-width">
            <label>التفاصيل</label>
            <span>{{ $order->order_details }}</span>
        </div>
        <div class="info-item">
            <label>الكلفة</label>
            <span>{{ number_format($order->cost, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
        </div>
        <div class="info-item">
            <label>حالة الطلبية</label>
            <span class="status {{ $order->status }}">
                @switch($order->status)
                    @case('new') جديدة @break
                    @case('in-progress') قيد التنفيذ @break
                    @case('delivered') تم التسليم @break
                    @case('cancelled') ملغاة @break
                @endswitch
            </span>
        </div>
        <div class="info-item">
            <label>تاريخ التسليم</label>
            <span>{{ $order->delivery_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-item">
            <label>مدقق الطلب</label>
            <span>{{ $order->reviewer->name ?? 'غير محدد' }}</span>
        </div>
        <div class="info-item">
            <label>المنفذ</label>
            <span>{{ $order->executor->name ?? 'غير محدد' }}</span>
        </div>
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    <div class="attachments-list">
        @foreach($order->attachments as $attachment)
            <div class="attachment-item">
                <div class="attachment-info">
                    <i data-lucide="{{ $attachment->file_icon }}"></i>
                    <span>{{ $attachment->file_name }}</span>
                </div>
                <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-attachment" onclick="return confirm('هل أنت متأكد من حذف هذا المرفق؟')">
                        <i data-lucide="trash-2"></i>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    
    <div class="add-attachment">
        <form method="POST" action="{{ route('orders.attachments', $order) }}" enctype="multipart/form-data">
            @csrf
            <div class="file-upload-area">
                <input type="file" class="file-upload-input" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.psd">
                <div class="file-upload-content">
                    <div class="file-upload-icon">
                        <i data-lucide="upload"></i>
                    </div>
                    <div class="file-upload-text">
                        <h4>اسحب الملفات هنا أو اضغط للتحديد</h4>
                        <p>PDF, Word, صور, Excel, Photoshop</p>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-secondary" style="margin-top: 10px;">
                <i data-lucide="plus"></i>
                إضافة مرفق
            </button>
        </form>
    </div>
    
    <!-- Audio Section -->
    <div class="section-header">
        <i data-lucide="mic"></i>
        <h3>المقاطع الصوتية</h3>
    </div>
    
    <div class="audio-list">
        @foreach($order->audioRecordings as $audio)
            <div class="audio-item">
                <span>{{ $audio->file_name }}</span>
                <audio controls>
                    <source src="{{ Storage::url($audio->file_path) }}" type="audio/wav">
                </audio>
                <form method="POST" action="{{ route('audio.destroy', $audio) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-audio" onclick="return confirm('هل أنت متأكد من حذف هذا التسجيل؟')">حذف</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection