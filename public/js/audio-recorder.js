function audioRecorder() {
    return {
        isRecording: false,
        hasRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioBlob: null,

        async startRecording() {
            try {
                // Show loading state
                const startBtn = document.querySelector('[x-on\\:click="startRecording()"]');
                if (startBtn) {
                    startBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> جاري التحضير...';
                    startBtn.disabled = true;
                    lucide.createIcons();
                }

                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.audioChunks, { type: 'audio/wav' });
                    const audioUrl = URL.createObjectURL(this.audioBlob);
                    this.$refs.audioPlayer.src = audioUrl;
                    this.hasRecording = true;
                };

                this.mediaRecorder.start();
                this.isRecording = true;
                
                // Reset button
                if (startBtn) {
                    startBtn.disabled = false;
                }
            } catch (error) {
                alert('خطأ في الوصول للميكروفون');
                // Reset button on error
                const startBtn = document.querySelector('[x-on\\:click="startRecording()"]');
                if (startBtn) {
                    startBtn.innerHTML = '<i data-lucide="mic"></i> بدء التسجيل';
                    startBtn.disabled = false;
                    lucide.createIcons();
                }
            }
        },

        stopRecording() {
            if (this.mediaRecorder) {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                this.isRecording = false;
            }
        },

        async saveRecording() {
            if (!this.audioBlob) return;

            const saveBtn = document.querySelector('[x-on\\:click="saveRecording()"]');
            if (saveBtn) {
                saveBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> جاري الحفظ...';
                saveBtn.disabled = true;
                lucide.createIcons();
            }

            const formData = new FormData();
            formData.append('audio', this.audioBlob, `recording-${Date.now()}.wav`);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const orderId = pathParts[pathParts.indexOf('orders') + 1];
            
            try {
                const response = await fetch(`/ayaz/public/orders/${orderId}/audio`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('خطأ في حفظ التسجيل');
                    if (saveBtn) {
                        saveBtn.innerHTML = '<i data-lucide="save"></i> حفظ التسجيل';
                        saveBtn.disabled = false;
                        lucide.createIcons();
                    }
                }
            } catch (error) {
                alert('خطأ في الشبكة');
                if (saveBtn) {
                    saveBtn.innerHTML = '<i data-lucide="save"></i> حفظ التسجيل';
                    saveBtn.disabled = false;
                    lucide.createIcons();
                }
            }
        },

        discardRecording() {
            this.hasRecording = false;
            this.audioBlob = null;
            this.audioChunks = [];
            this.$refs.audioPlayer.src = '';
        }
    }
}