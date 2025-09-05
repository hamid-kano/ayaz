<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.querySelector('.notification-btn');
    const dropdown = document.querySelector('.notifications-dropdown');
    const notificationsList = document.querySelector('.notifications-list');
    const badge = document.querySelector('.notification-badge');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    

    
    if (notificationBtn && dropdown) {
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');

            
            // Load notifications when dropdown opens
            if (dropdown.classList.contains('show')) {
                loadNotifications();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }
        
        // Load initial count and notifications
        loadUnreadCount();
        loadNotifications();
    }
    
    function loadUnreadCount() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error loading count:', error));
    }
    
    function loadNotifications() {
        if (!notificationsList) return;
        
        console.log('Loading notifications...');
        notificationsList.innerHTML = '<div style="text-align: center; padding: 20px; color: #6b7280;">جاري التحميل...</div>';
        
        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                // console.log('Notifications loaded:', data);
                displayNotifications(data.notifications);
                updateUnreadCount(data.unread_count);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsList.innerHTML = '<div style="text-align: center; padding: 20px; color: #ef4444;">خطأ في تحميل الإشعارات</div>';
            });
    }
    
    function displayNotifications(notifications) {
        if (!notificationsList) return;
        
        if (notifications.length === 0) {
            notificationsList.innerHTML = `
                <div class="empty-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>لا توجد إشعارات</p>
                </div>
            `;
            return;
        }
        
        notificationsList.innerHTML = notifications.map(notification => `
            <div class="notification-item ${!notification.read ? 'unread' : ''}" data-id="${notification.id}">
                <i class="fas fa-${notification.icon}"></i>
                <div class="notification-content">
                    <h5>${notification.title}</h5>
                    <p>${notification.message}</p>
                    <span class="notification-time">${notification.time}</span>
                </div>
                ${!notification.read ? '<button class="mark-read-btn" onclick="markAsRead(' + notification.id + ')"><i class="fas fa-check"></i></button>' : ''}
            </div>
        `).join('');
    }
    
    function updateUnreadCount(count) {
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }
    
    function markAllAsRead() {
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
                updateUnreadCount(0);
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    }
    
    // Global function for marking single notification as read
    window.markAsRead = function(notificationId) {
        fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
                updateUnreadCount(data.unread_count);
            }
        })
        .catch(error => console.error('Error marking as read:', error));
    };
});
</script>