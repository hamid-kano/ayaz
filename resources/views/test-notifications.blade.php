<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار الإشعارات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/lucide.js') }}"></script>
</head>
<body>
    <div class="app-container">
        <header class="header">
            <div class="header-content">
                <h1 class="app-title">اختبار الإشعارات</h1>
                <div class="header-actions">
                    <div class="notification-btn" id="notificationBtn">
                        <i data-lucide="bell"></i>
                        <span class="notification-badge" style="display: none;">0</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Notifications Dropdown -->
        <div class="dropdown-menu notifications-dropdown" id="notificationsDropdown">
            <div class="dropdown-header">
                <h4>الإشعارات</h4>
                <button class="mark-all-read" title="قراءة الكل">
                    <i data-lucide="check-double"></i>
                </button>
            </div>
            <div class="notifications-list">
                <!-- سيتم تحميل الإشعارات هنا عبر JavaScript -->
            </div>
            <div class="dropdown-footer">
                <a href="{{ route('notifications.index') }}" class="view-all-notifications">عرض جميع الإشعارات</a>
            </div>
        </div>

        <main class="main-content">
            <div style="padding: 20px;">
                <h2>اختبار نظام الإشعارات</h2>
                <p>اضغط على أيقونة الجرس لعرض الإشعارات</p>
                
                <div style="margin-top: 20px;">
                    <button onclick="testNotificationAPI()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 8px;">
                        اختبار API الإشعارات
                    </button>
                </div>
                
                <div id="testResults" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; display: none;">
                    <h3>نتائج الاختبار:</h3>
                    <pre id="testOutput"></pre>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            const notificationBtn = document.querySelector('.notification-btn');
            const dropdown = document.querySelector('.notifications-dropdown');
            const notificationsList = document.querySelector('.notifications-list');
            const badge = document.querySelector('.notification-badge');
            const markAllReadBtn = document.querySelector('.mark-all-read');
            
            console.log('Test page loaded');
            console.log('Elements found:', {
                notificationBtn: !!notificationBtn,
                dropdown: !!dropdown,
                notificationsList: !!notificationsList,
                badge: !!badge
            });
            
            if (notificationBtn && dropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                    console.log('Dropdown toggled, visible:', dropdown.classList.contains('show'));
                    
                    if (dropdown.classList.contains('show')) {
                        loadNotifications();
                    }
                });
                
                document.addEventListener('click', function(e) {
                    if (!notificationBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
                
                if (markAllReadBtn) {
                    markAllReadBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        markAllAsRead();
                    });
                }
                
                loadUnreadCount();
                loadNotifications();
            }
            
            function loadUnreadCount() {
                fetch('{{ route("notifications.unread-count") }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Unread count:', data.count);
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
                        console.log('Notifications loaded:', data);
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
        
        function testNotificationAPI() {
            const testResults = document.getElementById('testResults');
            const testOutput = document.getElementById('testOutput');
            
            testResults.style.display = 'block';
            testOutput.textContent = 'جاري اختبار APIs...';
            
            Promise.all([
                fetch('{{ route("notifications.unread-count") }}').then(r => r.json()),
                fetch('{{ route("notifications.recent") }}').then(r => r.json())
            ])
            .then(([countData, recentData]) => {
                testOutput.textContent = JSON.stringify({
                    unread_count: countData,
                    recent_notifications: recentData
                }, null, 2);
            })
            .catch(error => {
                testOutput.textContent = 'خطأ: ' + error.message;
            });
        }
    </script>
</body>
</html>