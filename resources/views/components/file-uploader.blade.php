@props(['orderId'])

<script>
function fileUploader() {
    return {
        files: [],
        isUploading: false,

        handleFiles(fileList) {
            for (let file of fileList) {
                this.files.push({
                    file: file,
                    name: file.name,
                    size: file.size,
                    progress: 0,
                    uploaded: false
                });
            }
        },

        handleDrop(event) {
            this.handleFiles(event.dataTransfer.files);
        },

        removeFile(index) {
            this.files.splice(index, 1);
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        async uploadFiles() {
            this.isUploading = true;
            
            const uploadPromises = this.files.map(fileObj => {
                if (fileObj.uploaded) return Promise.resolve();
                
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('attachments[]', fileObj.file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    
                    const xhr = new XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            fileObj.progress = Math.round((e.loaded / e.total) * 100);
                        }
                    });
                    
                    xhr.onload = () => {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            fileObj.uploaded = true;
                            fileObj.progress = 100;
                            this.addAttachmentToList(response.attachments[0]);
                            resolve();
                        } else {
                            reject(new Error('Upload failed'));
                        }
                    };
                    
                    xhr.onerror = () => reject(new Error('Network error'));
                    
                    xhr.open('POST', '{{ route("orders.attachments", $order->id ?? 0) }}');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.send(formData);
                });
            });
            
            try {
                await Promise.all(uploadPromises);
                this.files = [];
            } catch (error) {
                console.error('Some uploads failed:', error);
            } finally {
                this.isUploading = false;
            }
        },
        
        addAttachmentToList(attachment) {
            const attachmentsList = document.querySelector('.attachments-list');
            const emptyState = attachmentsList.querySelector('.empty-attachments');
            
            if (emptyState) {
                emptyState.remove();
            }
            
            const attachmentItem = document.createElement('div');
            attachmentItem.className = 'attachment-item';
            const attachmentRoute = '{{ route('attachments.destroy', 'ATTACHMENT_ID') }}'.replace('ATTACHMENT_ID', attachment.id);
            attachmentItem.innerHTML = `
                <div class="attachment-info">
                    <i data-lucide="file"></i>
                    <div class="attachment-details">
                        <span class="attachment-name">${attachment.file_name}</span>
                        <small class="attachment-size">${(attachment.file_size / 1024).toFixed(1)} KB</small>
                    </div>
                </div>
                <div class="attachment-actions">
                    <a href="{{ url('/') }}/${attachment.file_path}" class="view-attachment" target="_blank" title="عرض">
                        <i data-lucide="eye"></i>
                    </a>
                    <form method="POST" action="${attachmentRoute}" style="display: inline;">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" onclick="showDeleteModal('${attachmentRoute}', 'المرفق', this.closest('form'))" title="حذف">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                </div>
            `;
            
            attachmentsList.appendChild(attachmentItem);
            lucide.createIcons();
        }
    }
}
</script>