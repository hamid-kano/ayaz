<script>
class NotificationManager {
    constructor() {
        this.init();
        this.loadNotifications();
        this.startPolling();
    }

    init() {
        this.notificationBtn = document.querySelector('.notification-btn');
        this.notificationBadge = document.querySelector('.notification-badge');
        this.notificationsDropdown = document.querySelector('.notifications-dropdown');
        this.notificationsList = document.querySelector('.notifications-list');
        this.markAllReadBtn = document.querySelector('.mark-all-read');
        
        this.bindEvents();
    }

    bindEvents() {
        if (this.notificationBtn) {
            this.notificationBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
        }

        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        document.addEventListener('click', (e) => {
            if (!this.notificationsDropdown?.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    async loadNotifications() {
        try {
            console.log('Loading notifications...');
            const response = await fetch('{{ route("notifications.recent") }}');
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Notifications data:', data);
            
            this.updateBadge(data.unread_count);
            this.renderNotifications(data.notifications);
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`{{ route("notifications.read", ":id") }}`.replace(':id', notificationId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateBadge(data.unread_count);
                this.markNotificationAsRead(notificationId);
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateBadge(0);
                this.markAllNotificationsAsRead();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    updateBadge(count) {
        if (this.notificationBadge) {
            if (count > 0) {
                this.notificationBadge.textContent = count > 99 ? '99+' : count;
                this.notificationBadge.style.display = 'flex';
            } else {
                this.notificationBadge.style.display = 'none';
            }
        }
    }

    renderNotifications(notifications) {
        if (!this.notificationsList) return;

        if (notifications.length === 0) {
            this.notificationsList.innerHTML = `
                <div class="empty-notifications">
                    <i data-lucide="bell-off"></i>
                    <p>لا توجد إشعارات</p>
                </div>
            `;
            return;
        }

        this.notificationsList.innerHTML = notifications.map(notification => `
            <div class="notification-item ${!notification.read ? 'unread' : ''}" 
                 data-id="${notification.id}">
                <i data-lucide="${notification.icon}"></i>
                <div class="notification-content">
                    <p>${notification.message}</p>
                    <span class="notification-time">${notification.time}</span>
                </div>
                ${!notification.read ? `
                    <button class="mark-read-btn" onclick="notificationManager.markAsRead(${notification.id})">
                        <i data-lucide="check"></i>
                    </button>
                ` : ''}
            </div>
        `).join('');

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    markNotificationAsRead(notificationId) {
        const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
        if (notificationElement) {
            notificationElement.classList.remove('unread');
            const markReadBtn = notificationElement.querySelector('.mark-read-btn');
            if (markReadBtn) {
                markReadBtn.remove();
            }
        }
    }

    markAllNotificationsAsRead() {
        const unreadNotifications = document.querySelectorAll('.notification-item.unread');
        unreadNotifications.forEach(notification => {
            notification.classList.remove('unread');
            const markReadBtn = notification.querySelector('.mark-read-btn');
            if (markReadBtn) {
                markReadBtn.remove();
            }
        });
    }

    toggleDropdown() {
        if (this.notificationsDropdown) {
            this.notificationsDropdown.classList.toggle('show');
        }
    }

    closeDropdown() {
        if (this.notificationsDropdown) {
            this.notificationsDropdown.classList.remove('show');
        }
    }

    startPolling() {
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});
</script>