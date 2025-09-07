@extends('layouts.app')

@section('title', 'تعديل المشترى - مطبعة آياز')

@section('content')
<div class="page-header">
    <a href="{{ route('purchases.index') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>تعديل المشترى</h2>
</div>

<form action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    @method('PUT')
    
    <!-- Purchase Info Section -->
    <div class="section-header">
        <i data-lucide="shopping-cart"></i>
        <h3>بيانات المشترى</h3>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>رقم المشترى</label>
            <input type="text" value="{{ $purchase->purchase_number }}" readonly class="readonly-input">
        </div>
        <div class="form-group">
            <label>تاريخ المشترى</label>
            <input type="date" name="purchase_date" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
            @error('purchase_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>المبلغ</label>
            <input type="number" name="amount" value="{{ old('amount', $purchase->amount) }}" min="0" step="0.01" required>
            @error('amount')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>العملة</label>
            <select name="currency" required>
                <option value="syp" {{ $purchase->currency == 'syp' ? 'selected' : '' }}>ليرة سورية</option>
                <option value="usd" {{ $purchase->currency == 'usd' ? 'selected' : '' }}>دولار أمريكي</option>
            </select>
            @error('currency')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="form-group">
        <label>حالة الدفع</label>
        <select name="status" required>
            <option value="cash" {{ $purchase->status == 'cash' ? 'selected' : '' }}>نقدي</option>
            <option value="debt" {{ $purchase->status == 'debt' ? 'selected' : '' }}>دين</option>
        </select>
        @error('status')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>اسم المورد</label>
        <input type="text" name="supplier" value="{{ old('supplier', $purchase->supplier) }}" required>
        @error('supplier')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>تفاصيل المشترى</label>
        <textarea name="details" rows="3" required>{{ old('details', $purchase->details) }}</textarea>
        @error('details')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <!-- Attachments Section -->
    <div class="section-header">
        <i data-lucide="paperclip"></i>
        <h3>المرفقات</h3>
    </div>
    
    @if($purchase->attachments->count() > 0)
    <div class="existing-attachments">
        <h4>المرفقات الحالية</h4>
        <div class="attachments-grid">
            @foreach($purchase->attachments as $attachment)
            <div class="attachment-item" data-attachment-id="{{ $attachment->id }}">
                <div class="attachment-info">
                    <i data-lucide="{{ $attachment->file_icon }}"></i>
                    <div class="attachment-details">
                        <span class="attachment-name">{{ $attachment->file_name }}</span>
                        <span class="attachment-size">{{ $attachment->file_size_formatted }}</span>
                    </div>
                </div>
                <div class="attachment-actions">
                    <a href="{{ asset($attachment->file_path) }}" target="_blank" class="btn-view">
                        <i data-lucide="eye"></i>
                    </a>
                    <button type="button" class="btn-delete" onclick="showDeleteModal('', '{{ $attachment->file_name }}'); window.deleteAttachmentId = {{ $attachment->id }};">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="form-group">
        <label>إضافة مرفقات جديدة</label>
        <div class="file-upload-area">
            <input type="file" class="file-upload-input" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
            <div class="file-upload-content">
                <div class="file-upload-icon">
                    <i data-lucide="upload"></i>
                </div>
                <div class="file-upload-text">
                    <h4>اسحب الملفات هنا أو اضغط للتحديد</h4>
                    <p>PDF وصور</p>
                </div>
            </div>
        </div>
        <div class="uploaded-files"></div>
    </div>
    
    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="submit-btn">
            <i data-lucide="save"></i>
            حفظ التعديلات
        </button>
    </div>
</form>

<script>
function deleteAttachment(attachmentId) {
        const deleteUrl = '{{ route('purchase.attachments.destroy', ':id') }}'.replace(':id', attachmentId);
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-attachment-id="${attachmentId}"]`).remove();
                // إذا لم تعد هناك مرفقات، إخفاء القسم
                const attachmentsGrid = document.querySelector('.attachments-grid');
                if (attachmentsGrid && attachmentsGrid.children.length === 0) {
                    document.querySelector('.existing-attachments').style.display = 'none';
                }
            } else {
                alert('حدث خطأ في حذف المرفق');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في حذف المرفق');
        });
}

// معالجة رفع الملفات الجديدة
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('.file-upload-input');
    const uploadedFiles = document.querySelector('.uploaded-files');
    const fileUploadArea = document.querySelector('.file-upload-area');
    
    if (fileInput && uploadedFiles && fileUploadArea) {
        fileInput.addEventListener('change', function(e) {
            displaySelectedFiles(e.target.files);
        });
        
        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        
        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
        });
        
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            displaySelectedFiles(e.dataTransfer.files);
        });
    }
    
    function displaySelectedFiles(files) {
        uploadedFiles.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'uploaded-file';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            
            const fileIcon = document.createElement('div');
            fileIcon.className = 'file-icon';
            fileIcon.innerHTML = getFileIcon(file.name);
            
            const fileDetails = document.createElement('div');
            fileDetails.className = 'file-details';
            
            const fileName = document.createElement('span');
            fileName.className = 'file-name';
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'file-size';
            fileSize.textContent = formatFileSize(file.size);
            
            fileDetails.appendChild(fileName);
            fileDetails.appendChild(fileSize);
            
            fileInfo.appendChild(fileIcon);
            fileInfo.appendChild(fileDetails);
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '<i data-lucide="x"></i>';
            removeBtn.onclick = function() {
                fileItem.remove();
                // إعادة تعيين الملفات
                const dt = new DataTransfer();
                Array.from(fileInput.files).forEach((f, i) => {
                    if (i !== index) dt.items.add(f);
                });
                fileInput.files = dt.files;
            };
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            
            uploadedFiles.appendChild(fileItem);
        });
        
        // تفعيل أيقونات Lucide
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        switch(extension) {
            case 'pdf': return '<i data-lucide="file-text"></i>';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': return '<i data-lucide="image"></i>';
            case 'doc':
            case 'docx': return '<i data-lucide="file-text"></i>';
            case 'xls':
            case 'xlsx': return '<i data-lucide="file-spreadsheet"></i>';
            case 'psd': return '<i data-lucide="layers"></i>';
            default: return '<i data-lucide="file"></i>';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }
});

// معالجة تأكيد الحذف من المودال
window.addEventListener('confirm-delete', function() {
    if (window.deleteAttachmentId) {
        deleteAttachment(window.deleteAttachmentId);
        window.deleteAttachmentId = null;
    }
});
</script>
@endsection