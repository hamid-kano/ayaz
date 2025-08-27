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

// Handle form submissions
function handleFormSubmit(formType) {
    showNotification(`تم حفظ ${formType} بنجاح`);
    
    // Navigate back to home after a delay
    setTimeout(() => {
        navigateToPage('home');
    }, 1500);
}

// Add event listeners for form submissions
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('submit-btn')) {
        e.preventDefault();
        handleFormSubmit('البيانات');
    }
    
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