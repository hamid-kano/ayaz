@extends('layouts.app')

@section('title', 'تفاصيل الطلبية - مطبعة ريناس')

@section('content')
    <div class="page-header">
        <a href="{{ route('orders.index') }}" class="back-btn">
            <i data-lucide="arrow-right"></i>
        </a>
        <h2>تفاصيل الطلبية</h2>
        <div class="header-actions">
            <a href="{{ route('orders.public-print', $order) }}" class="btn-print" target="_blank">
                <i data-lucide="printer"></i>
                طباعة
            </a>
            <a href="{{ route('orders.edit', $order) }}" class="add-btn">
                <i data-lucide="edit-2"></i>
            </a>
        </div>
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
                <span class="cost-value">
                    @if($order->total_cost_syp > 0 && $order->total_cost_usd > 0)
                        {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ليرة + {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} دولار
                    @elseif($order->total_cost_syp > 0)
                        {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ليرة
                    @elseif($order->total_cost_usd > 0)
                        {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} دولار
                    @else
                        0 ليرة
                    @endif
                </span>
            </div>
            <div class="info-item">
                <label>حالة الطلبية</label>
                <span class="status {{ $order->status }}">
                    @switch($order->status)
                        @case('new')
                            جديدة
                        @break

                        @case('in-progress')
                            قيد التنفيذ
                        @break

                        @case('delivered')
                            تم التسليم
                        @break

                        @case('cancelled')
                            ملغاة
                        @break
                    @endswitch
                </span>
            </div>
            <div class="info-item">
                <label>تاريخ التسليم</label>
                <span>{{ $order->delivery_date->format('Y-m-d') }}</span>
            </div>
            <div class="info-item">
                <label>مدقق الطلب</label>
                <span>{{ $order->reviewer_name ?? 'غير محدد' }}</span>
            </div>
            <div class="info-item">
                <label>المنفذ</label>
                <span>{{ $order->executor->name ?? 'غير محدد' }}</span>
            </div>
            <div class="info-item full-width">
                <label>ملاحظات</label>
                <span class="details-text">{{ $order->order_details }}</span>
            </div>
        </div>

        <!-- Order Items Section -->
        @include('components.order-items', ['items' => $order->items, 'editable' => false])

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
                        <a href="{{ asset($attachment->file_path) }}" class="view-attachment" target="_blank"
                            title="عرض">
                            <i data-lucide="eye"></i>
                        </a>
                        <form method="POST" action="{{ route('attachments.destroy', $attachment) }}"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                onclick="showDeleteModal('{{ route('attachments.destroy', $attachment) }}', 'المرفق', this.closest('form'))"
                                title="حذف">
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

        <div class="add-attachment" x-data="fileUploader()">
            <div class="file-upload-area" @drop.prevent="handleDrop($event)" @dragover.prevent @dragenter.prevent>
                <input type="file" x-ref="fileInput" @change="handleFiles($event.target.files)" multiple
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.psd" style="display: none;">
                <div class="file-upload-content" @click="$refs.fileInput.click()">
                    <div class="file-upload-icon">
                        <i data-lucide="upload"></i>
                    </div>
                    <div class="file-upload-text">
                        <h4>اسحب الملفات هنا أو اضغط للتحديد</h4>
                        <p>PDF, Word, صور, Excel, Photoshop</p>
                    </div>
                </div>
            </div>

            <div x-show="files.length > 0" class="upload-queue">
                <template x-for="(file, index) in files" :key="index">
                    <div class="upload-item">
                        <div class="file-info">
                            <i data-lucide="file"></i>
                            <span x-text="file.name"></span>
                            <small x-text="formatFileSize(file.size)"></small>
                        </div>
                        <div class="upload-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" :style="`width: ${file.progress}%`"></div>
                            </div>
                            <span class="progress-text" x-text="file.progress + '%'"></span>
                        </div>
                        <button type="button" @click="removeFile(index)" class="remove-file" x-show="file.progress === 0">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" @click="uploadFiles()" x-show="files.length > 0 && !isUploading" class="btn-primary">
                <i data-lucide="upload"></i>
                رفع الملفات
            </button>
        </div>

        @include('components.file-uploader')

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
                        <source src="{{ URL($audio->file_path) }}" type="audio/wav">
                    </audio>
                    <form method="POST" action="{{ route('audio.destroy', $audio) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            onclick="showDeleteModal('{{ route('audio.destroy', $audio) }}', 'التسجيل الصوتي', this.closest('form'))">حذف</button>
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
                <button type="button" x-show="!isRecording && !hasRecording" @click="startRecording()"
                    class="btn-primary">
                    <i data-lucide="mic"></i>
                    بدء التسجيل
                </button>

                <button type="button" x-show="isRecording" @click="stopRecording()" class="btn-danger">
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

        @include('components.audio-recorder')
    </div>

@push('styles')
<style>
.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-print {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-print:hover {
    background: #218838;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.btn-print i {
    width: 16px;
    height: 16px;
}
</style>
@endpush

@endsection