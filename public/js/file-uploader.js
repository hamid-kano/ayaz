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
            const orderId = window.location.pathname.split('/')[2];

            for (let fileObj of this.files) {
                if (fileObj.uploaded) continue;

                const formData = new FormData();
                formData.append('attachments[]', fileObj.file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                try {
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
                        }
                    };

                    xhr.open('POST', `/orders/${orderId}/attachments`);
                    xhr.send(formData);

                    await new Promise(resolve => {
                        xhr.onloadend = resolve;
                    });

                } catch (error) {
                    console.error('Upload failed:', error);
                }
            }

            this.isUploading = false;
            
            // Reload page after all uploads complete
            if (this.files.every(f => f.uploaded)) {
                setTimeout(() => location.reload(), 500);
            }
        }
    }
}