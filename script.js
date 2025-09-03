// Initialize Lucide Icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Add loading animation
    showLoadingAnimation();
    
    // Initialize app after loading
    setTimeout(() => {
        initializeApp();
    }, 2000);
});

// Global variables
let currentPage = 'home';

function showLoadingAnimation() {
    const loading = document.createElement('div');
    loading.className = 'loading';
    document.body.appendChild(loading);
}

function initializeApp() {
    // Menu item click handlers
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const page = this.dataset.page;
            navigateToPage(page);
        });
        
        // Add touch feedback
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        item.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Bottom navigation
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const page = this.dataset.page;
            if (page) {
                navigateToPage(page);
                updateBottomNav(page);
            }
        });
    });
    
    // Back buttons
    const backBtns = document.querySelectorAll('.back-btn');
    backBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const backPage = this.dataset.back;
            navigateToPage(backPage);
        });
    });
    
    // User menu dropdown
    const userMenu = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');
    const overlay = document.getElementById('overlay');
    
    userMenu.addEventListener('click', function() {
        userDropdown.classList.toggle('active');
        overlay.classList.toggle('active');
    });
    
    // Notifications dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    notificationBtn.addEventListener('click', function() {
        notificationsDropdown.classList.toggle('active');
        overlay.classList.toggle('active');
        // Close user dropdown if open
        userDropdown.classList.remove('active');
    });
    
    // Close dropdowns when clicking overlay
    overlay.addEventListener('click', function() {
        userDropdown.classList.remove('active');
        notificationsDropdown.classList.remove('active');
        overlay.classList.remove('active');
    });
    
    // Add scroll animations
    addScrollAnimations();
}

// Navigation function
function navigateToPage(page) {
    // Hide all pages
    const pages = document.querySelectorAll('.page');
    pages.forEach(p => p.classList.remove('active'));
    
    // Show target page
    const targetPage = document.getElementById(page + 'Page');
    if (targetPage) {
        targetPage.classList.add('active');
        currentPage = page;
        
        // Update bottom navigation
        updateBottomNav(page);
        
        // Re-initialize Lucide icons for new content
        setTimeout(() => {
            lucide.createIcons();
        }, 100);
    }
}

function updateBottomNav(page) {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.classList.remove('active');
        if (item.dataset.page === page) {
            item.classList.add('active');
        }
    });
    
    // Special case for home page
    if (page === 'home') {
        navItems[0].classList.add('active');
    }
}

function createRipple(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        font-weight: 500;
        max-width: 300px;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function addScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            }
        });
    });
    
    document.querySelectorAll('.menu-item').forEach(item => {
        observer.observe(item);
    });
}

// Add CSS for ripple animation and page transitions
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(style);

// Add swipe gestures for mobile
let startX, startY;

document.addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
});

document.addEventListener('touchmove', function(e) {
    if (!startX || !startY) return;
    
    const diffX = startX - e.touches[0].clientX;
    const diffY = startY - e.touches[0].clientY;
    
    if (Math.abs(diffX) > Math.abs(diffY)) {
        if (diffX > 50) {
            // Swipe left
            console.log('Swiped left');
        } else if (diffX < -50) {
            // Swipe right
            console.log('Swiped right');
        }
    }
    
    startX = null;
    startY = null;
});

// Add haptic feedback for supported devices
function addHapticFeedback() {
    if ('vibrate' in navigator) {
        document.querySelectorAll('.menu-item, .nav-item').forEach(item => {
            item.addEventListener('click', () => {
                navigator.vibrate(50);
            });
        });
    }
}

// Initialize haptic feedback
addHapticFeedback();

// Form functions
function clearForm() {
    document.querySelectorAll('#newOrderPage input, #newOrderPage select, #newOrderPage textarea').forEach(field => {
        field.value = '';
    });
    showNotification('تم مسح النموذج');
}

// Audio recording functionality
let mediaRecorder;
let audioChunks = [];
let recordingCount = 0;

function initializeAudioRecorder() {
    const recordBtn = document.getElementById('recordBtn');
    if (!recordBtn) return;
    
    recordBtn.addEventListener('click', toggleRecording);
}

function toggleRecording() {
    const recordBtn = document.getElementById('recordBtn');
    
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        recordBtn.innerHTML = '<i data-lucide="mic"></i> بدء التسجيل';
        recordBtn.classList.remove('recording');
    } else {
        startRecording();
    }
}

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                addAudioToList(audioBlob);
                stream.getTracks().forEach(track => track.stop());
            };
            
            mediaRecorder.start();
            
            const recordBtn = document.getElementById('recordBtn');
            recordBtn.innerHTML = '<i data-lucide="square"></i> إيقاف التسجيل';
            recordBtn.classList.add('recording');
            
            lucide.createIcons();
        })
        .catch(err => {
            showNotification('لا يمكن الوصول للميكروفون');
        });
}

function addAudioToList(audioBlob) {
    recordingCount++;
    const audioList = document.getElementById('audioList');
    const audioItem = document.createElement('div');
    audioItem.className = 'audio-item';
    
    const audioUrl = URL.createObjectURL(audioBlob);
    
    audioItem.innerHTML = `
        <span>تسجيل ${recordingCount}</span>
        <audio controls src="${audioUrl}"></audio>
        <button type="button" class="delete-audio" onclick="deleteAudio(this)">حذف</button>
    `;
    
    audioList.appendChild(audioItem);
}

function deleteAudio(button) {
    button.parentElement.remove();
}

function saveOrder() {
    // Generate order number
    const orderNumber = 'ORD-' + Date.now();
    document.getElementById('orderNumber').value = orderNumber;
    
    const customerName = document.getElementById('customerName').value;
    const orderType = document.getElementById('orderType').value;
    const orderDetails = document.getElementById('orderDetails').value;
    const orderCost = document.getElementById('orderCost').value;
    
    if (!customerName || !orderType || !orderDetails || !orderCost) {
        showNotification('يرجى ملء جميع الحقول المطلوبة');
        return;
    }
    
    showNotification('تم حفظ الطلبية بنجاح');
    
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1500);
}

// Filter functionality
function initializeFilters() {
    const filterBtns = document.querySelectorAll('.tab-btn');
    const listItems = document.querySelectorAll('.list-item[data-category]');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active tab
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            listItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

// Add event listeners for form submissions and actions
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-btn')) {
        showNotification('فتح نموذج الإضافة');
    }
    
    if (e.target.closest('.dropdown-item')) {
        const item = e.target.closest('.dropdown-item');
        const text = item.querySelector('span').textContent;
        showNotification(`تم اختيار: ${text}`);
        
        // Close dropdown
        document.getElementById('userDropdown').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    }
    
    if (e.target.closest('.action-btn.edit')) {
        showNotification('فتح نموذج التعديل');
    }
    
    if (e.target.closest('.action-btn.delete')) {
        if (confirm('هل أنت متأكد من الحذف؟')) {
            const listItem = e.target.closest('.list-item');
            listItem.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                listItem.remove();
                showNotification('تم الحذف بنجاح');
            }, 300);
        }
    }
});

// Orders search functionality
function initializeOrdersSearch() {
    const searchOrders = document.getElementById('searchOrders');
    
    if (searchOrders) {
        searchOrders.addEventListener('input', filterOrders);
    }
}

function filterOrders() {
    const searchTerm = document.getElementById('searchOrders')?.value.toLowerCase() || '';
    const orderCards = document.querySelectorAll('.order-card');
    
    orderCards.forEach(card => {
        const orderNumber = card.dataset.order.toLowerCase();
        const customerName = card.dataset.customer.toLowerCase();
        const orderType = card.dataset.type.toLowerCase();
        
        const matches = orderNumber.includes(searchTerm) || 
                       customerName.includes(searchTerm) || 
                       orderType.includes(searchTerm);
        
        card.style.display = matches || searchTerm === '' ? 'block' : 'none';
    });
}

// File upload functionality
function initializeFileUpload() {
    const fileUploadAreas = document.querySelectorAll('.file-upload-area');
    
    fileUploadAreas.forEach(area => {
        const input = area.querySelector('.file-upload-input');
        const uploadedFilesContainer = area.parentElement.querySelector('.uploaded-files');
        
        if (!input) return;
        
        // Drag and drop events
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files, uploadedFilesContainer);
        });
        
        // File input change
        input.addEventListener('change', (e) => {
            handleFiles(e.target.files, uploadedFilesContainer);
        });
    });
}

function handleFiles(files, container) {
    if (!container) return;
    
    Array.from(files).forEach(file => {
        addFileToList(file, container);
    });
}

function addFileToList(file, container) {
    const fileElement = document.createElement('div');
    fileElement.className = 'uploaded-file';
    
    const fileSize = (file.size / 1024).toFixed(1) + ' KB';
    const fileIcon = getFileIcon(file.name);
    
    fileElement.innerHTML = `
        <div class="file-info">
            <div class="file-icon">
                <i data-lucide="${fileIcon}"></i>
            </div>
            <div class="file-details">
                <div class="file-name">${file.name}</div>
                <div class="file-size">${fileSize}</div>
            </div>
        </div>
        <button class="remove-file" onclick="removeFile(this)">
            <i data-lucide="x"></i>
        </button>
    `;
    
    container.appendChild(fileElement);
    lucide.createIcons();
}

function getFileIcon(fileName) {
    const extension = fileName.toLowerCase().split('.').pop();
    
    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) return 'image';
    if (['pdf'].includes(extension)) return 'file-text';
    if (['doc', 'docx'].includes(extension)) return 'file-text';
    if (['xls', 'xlsx'].includes(extension)) return 'file-spreadsheet';
    if (['psd'].includes(extension)) return 'layers';
    
    return 'file';
}

function removeFile(button) {
    button.parentElement.remove();
}

// Initialize filters and audio recorder when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeFilters();
        initializeAudioRecorder();
        initializeOrdersSearch();
        initializeFileUpload();
        
        // Set current date
        const today = new Date().toISOString().split('T')[0];
        const orderDateField = document.getElementById('orderDate');
        if (orderDateField) {
            orderDateField.value = today;
        }
    }, 2100);
});

// Mark notifications as read when clicked
document.addEventListener('click', function(e) {
    if (e.target.closest('.notification-item')) {
        const notification = e.target.closest('.notification-item');
        notification.classList.remove('unread');
        
        // Update notification badge
        const badge = document.querySelector('.notification-badge');
        const currentCount = parseInt(badge.textContent);
        if (currentCount > 0) {
            badge.textContent = currentCount - 1;
            if (currentCount - 1 === 0) {
                badge.style.display = 'none';
            }
        }
    }
});