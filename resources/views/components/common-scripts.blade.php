<script>
// Loading states for submit buttons
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML || submitBtn.value;
                const loadingText = getLoadingText(form, submitBtn);
                
                submitBtn.disabled = true;
                if (submitBtn.innerHTML !== undefined) {
                    submitBtn.innerHTML = `<i data-lucide="loader-2" class="animate-spin"></i> ${loadingText}`;
                    lucide.createIcons();
                } else {
                    submitBtn.value = loadingText;
                }
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    if (submitBtn.innerHTML !== undefined) {
                        submitBtn.innerHTML = originalText;
                        lucide.createIcons();
                    } else {
                        submitBtn.value = originalText;
                    }
                }, 10000);
            }
        });
    });
    
    function getLoadingText(form, button) {
        const action = form.action.toLowerCase();
        const buttonText = (button.innerHTML || button.value).toLowerCase();
        
        if (action.includes('login') || buttonText.includes('دخول')) {
            return 'جاري تسجيل الدخول...';
        }
        if (action.includes('register') || buttonText.includes('إنشاء') || buttonText.includes('تسجيل')) {
            return 'جاري التسجيل...';
        }
        if (form.method.toLowerCase() === 'post' && !action.includes('update')) {
            return 'جاري الإضافة...';
        }
        if (action.includes('update') || buttonText.includes('تعديل') || buttonText.includes('تحديث')) {
            return 'جاري التعديل...';
        }
        if (buttonText.includes('حفظ')) {
            return 'جاري الحفظ...';
        }
        if (buttonText.includes('إرسال')) {
            return 'جاري الإرسال...';
        }
        return 'جاري المعالجة...';
    }
});

// Audio Recorder Function
function audioRecorder() {
    return {
        isRecording: false,
        hasRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioBlob: null,

        async startRecording() {
            try {
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
                
                if (startBtn) {
                    startBtn.disabled = false;
                }
            } catch (error) {
                alert('خطأ في الوصول للميكروفون');
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
            
            try {
                const response = await fetch('{{ route("orders.audio", $order->id ?? 0) }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    this.addAudioToList(data.audio);
                    this.discardRecording();
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

        addAudioToList(audio) {
            const audioList = document.querySelector('.audio-list');
            const emptyState = audioList.querySelector('.empty-audio');
            
            if (emptyState) {
                emptyState.remove();
            }
            
            const audioItem = document.createElement('div');
            audioItem.className = 'audio-item';
            audioItem.innerHTML = `
                <span>${audio.file_name}</span>
                <audio controls>
                    <source src="/audio/${audio.filename}" type="audio/wav">
                </audio>
                <form method="POST" action="/audio/${audio.id}" style="display: inline;">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" onclick="showDeleteModal('/audio/${audio.id}', 'التسجيل الصوتي', this.closest('form'))">حذف</button>
                </form>
            `;
            
            audioList.appendChild(audioItem);
        },

        discardRecording() {
            this.hasRecording = false;
            this.audioBlob = null;
            this.audioChunks = [];
            this.$refs.audioPlayer.src = '';
        }
    }
}

// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Notifications toggle
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    if (notificationBtn && notificationsDropdown) {
        notificationBtn.addEventListener('click', function() {
            notificationsDropdown.classList.toggle('show');
            userDropdown?.classList.remove('show');
        });
    }
    
    // User menu toggle
    const userMenu = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function() {
            userDropdown.classList.toggle('show');
            notificationsDropdown?.classList.remove('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!userMenu?.contains(e.target)) {
            userDropdown?.classList.remove('show');
        }
        if (!notificationBtn?.contains(e.target)) {
            notificationsDropdown?.classList.remove('show');
        }
    });
});
</script>