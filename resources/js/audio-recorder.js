function audioRecorder() {
    return {
        isRecording: false,
        hasRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioBlob: null,

        async startRecording() {
            try {
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
            } catch (error) {
                alert('خطأ في الوصول للميكروفون');
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

            const formData = new FormData();
            formData.append('audio', this.audioBlob, `recording-${Date.now()}.wav`);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch(`/orders/${window.location.pathname.split('/')[2]}/audio`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('خطأ في حفظ التسجيل');
                }
            } catch (error) {
                alert('خطأ في الشبكة');
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