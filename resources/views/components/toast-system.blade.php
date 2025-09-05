<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<script>
class ToastSystem {
    constructor() {
        this.container = document.getElementById('toast-container');
    }

    show(message, type = 'info', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = this.getIcon(type);
        toast.innerHTML = `
            <div class="toast-content">
                <i class="${icon}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        this.container.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    getIcon(type) {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    success(message) { this.show(message, 'success'); }
    error(message) { this.show(message, 'error'); }
    warning(message) { this.show(message, 'warning'); }
    info(message) { this.show(message, 'info'); }
}

// Global toast instance
window.toast = new ToastSystem();

// Override alert function
window.alert = function(message) {
    toast.info(message);
};
</script>

<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    border-left: 4px solid #667eea;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 300px;
    max-width: 400px;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.toast.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-success {
    border-left-color: #10b981;
}

.toast-error {
    border-left-color: #ef4444;
}

.toast-warning {
    border-left-color: #f59e0b;
}

.toast-info {
    border-left-color: #3b82f6;
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.toast-content i {
    font-size: 18px;
}

.toast-success .toast-content i {
    color: #10b981;
}

.toast-error .toast-content i {
    color: #ef4444;
}

.toast-warning .toast-content i {
    color: #f59e0b;
}

.toast-info .toast-content i {
    color: #3b82f6;
}

.toast-content span {
    color: #374151;
    font-weight: 500;
    line-height: 1.4;
}

.toast-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.toast-close:hover {
    background: #f3f4f6;
    color: #6b7280;
}

@media (max-width: 480px) {
    .toast-container {
        right: 10px;
        left: 10px;
    }
    
    .toast {
        min-width: auto;
        max-width: none;
    }
}
</style>