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
                            fileObj.uploaded = true;
                            fileObj.progress = 100;
                            resolve();
                        } else {
                            reject(new Error('Upload failed'));
                        }
                    };
                    
                    xhr.onerror = () => reject(new Error('Network error'));
                    
                    xhr.open('POST', '{{ route("orders.attachments", $orderId) }}');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.send(formData);
                });
            });
            
            try {
                await Promise.all(uploadPromises);
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                console.error('Some uploads failed:', error);
            } finally {
                this.isUploading = false;
            }
        }
    }
}
</script>