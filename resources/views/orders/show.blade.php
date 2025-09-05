@extends('layouts.app')

@section('title', 'تفاصيل الطلبية - مطبعة ريناس')

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
    <div class="info-grid">
        <div class="info-item">
            <label>رقم الطلبية</label>
            <span>#{{ $order->order_number }}</span>
        </div>
        <div class="info-item">
            <label>تاريخ الطلب</label>
            <span>{{ $order->order_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-item">
            <label>اسم العميل</label>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="info-item">
            <label>نوع الطلبية</label>
            <span>{{ $order->order_type }}</span>
        </div>
        <div class="info-item">
            <label>الكلفة</label>
            <span class="cost-value">{{ number_format($order->cost, 2) }} {{ $order->currency == 'usd' ? 'دولار' : 'ليرة' }}</span>
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
        <div class="info-item full-width">
            <label>تفاصيل الطلبية</label>
            <span class="details-text">{{ $order->order_details }}</span>
        </div>
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    <div class="attachments-list">
        @forelse($order->attachments as $attachment)
            <div class="attachment-item">
                <div class="attachment-info">
                    <i data-lucide="file"></i>
                    <div class="attachment-details">
                        <span class="attachment-name">{{ $attachment->file_name }}</span>
                        <small class="attachment-size">{{ number_format($attachment->file_size / 1024, 1) }} KB</small>
                    </div>
                </div>
                <div class="attachment-actions">
                    <a href="{{ Storage::url($attachment->file_path) }}" class="view-attachment" target="_blank" title="عرض">
                        <i data-lucide="eye"></i>
                    </a>
                    <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="showDeleteModal('{{ route('attachments.destroy', $attachment) }}', 'المرفق', this.closest('form'))" title="حذف">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-attachments">
                <i data-lucide="paperclip"></i>
                <p>لا توجد مرفقات</p>
            </div>
        @endforelse
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
        @forelse($order->audioRecordings as $audio)
            <div class="audio-item">
                <span>{{ $audio->file_name }}</span>
                <audio controls>
                    <source src="{{ Storage::url($audio->file_path) }}" type="audio/wav">
                </audio>
                <form method="POST" action="{{ route('audio.destroy', $audio) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="showDeleteModal('{{ route('audio.destroy', $audio) }}', 'التسجيل الصوتي', this.closest('form'))">حذف</button>
                </form>
            </div>
        @empty
            <div class="empty-audio">
                <i data-lucide="mic"></i>
                <p>لا توجد تسجيلات صوتية</p>
            </div>
        @endforelse
    </div>
    
    <!-- Audio Recording Interface -->
    <div class="audio-recorder" x-data="audioRecorder()">
        <div class="recorder-controls">
            <button type="button" 
                    x-show="!isRecording && !hasRecording" 
                    @click="startRecording()" 
                    class="btn-primary">
                <i data-lucide="mic"></i>
                بدء التسجيل
            </button>
            
            <button type="button" 
                    x-show="isRecording" 
                    @click="stopRecording()" 
                    class="btn-danger">
                <i data-lucide="square"></i>
                إيقاف التسجيل
            </button>
            
            <div x-show="hasRecording" class="recording-preview">
                <audio x-ref="audioPlayer" controls></audio>
                <div class="recording-actions">
                    <button type="button" @click="saveRecording()" class="btn-success">
                        <i data-lucide="save"></i>
                        حفظ
                    </button>
                    <button type="button" @click="discardRecording()" class="btn-secondary">
                        <i data-lucide="x"></i>
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
        
        <div x-show="isRecording" class="recording-indicator">
            <div class="pulse-dot"></div>
            <span>جاري التسجيل...</span>
        </div>
    </div>
</div>
@endsection