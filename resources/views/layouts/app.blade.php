<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'مطبعة ريناس')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <script src="{{ asset('js/lucide.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @stack('styles')
</head>
<body class="{{ request()->routeIs('dashboard') ? 'dashboard' : '' }}">
    <div class="app-container">
        @auth
        @if(request()->routeIs('dashboard'))
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <h1 class="app-title">مطبعة ريناس</h1>
                <div class="header-actions">
                    <div class="notification-btn" id="notificationBtn">
                        <i data-lucide="bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="user-avatar" id="userMenu">
                        <i data-lucide="user"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Notifications Dropdown -->
        <div class="dropdown-menu notifications-dropdown" id="notificationsDropdown">
            <div class="dropdown-header">
                <h4>الإشعارات</h4>
                <span class="notifications-count">3</span>
            </div>
            <div class="notifications-list">
                <div class="notification-item unread">
                    <i data-lucide="package"></i>
                    <div class="notification-content">
                        <p>طلبية جديدة من أحمد محمد</p>
                        <span class="notification-time">منذ 5 دقائق</span>
                    </div>
                </div>
                <div class="notification-item unread">
                    <i data-lucide="clock"></i>
                    <div class="notification-content">
                        <p>موعد تسليم طلبية #1001</p>
                        <span class="notification-time">منذ ساعة</span>
                    </div>
                </div>
                <div class="notification-item">
                    <i data-lucide="check-circle"></i>
                    <div class="notification-content">
                        <p>تم تسليم طلبية #999</p>
                        <span class="notification-time">منذ 3 ساعات</span>
                    </div>
                </div>
            </div>
            <div class="dropdown-footer">
                <a href="{{ route('notifications.index') }}" class="view-all-notifications">عرض جميع الإشعارات</a>
            </div>
        </div>

        <!-- User Menu Dropdown -->
        <div class="dropdown-menu user-dropdown" id="userDropdown">
            <div class="dropdown-item">
                <i data-lucide="user-circle"></i>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <div class="dropdown-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item logout">
                    <i data-lucide="log-out"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
        @endif
        @endauth

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>

        <!-- Delete Modal -->
        @include('components.delete-modal')

        @auth
        <!-- Simple Bottom Navigation -->
        <nav class="modern-bottom-nav">
            <div class="nav-container">
                <a href="{{ route('dashboard') }}" class="modern-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="home"></i>
                    <span class="nav-label">الرئيسية</span>
                </a>
                
                <a href="{{ route('reports.index') }}" class="modern-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i data-lucide="bar-chart-3"></i>
                    <span class="nav-label">التقارير</span>
                </a>
                
                <a href="{{ route('settings.index') }}" class="modern-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i data-lucide="settings"></i>
                    <span class="nav-label">الإعدادات</span>
                </a>
            </div>
        </nav>
        @endauth
    </div>

    <!-- Scripts -->
    <script>
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
    
    <script src="{{ asset('js/delete-modal.js') }}"></script>
    <script src="{{ asset('js/audio-recorder.js') }}"></script>
    <script src="{{ asset('js/file-uploader.js') }}"></script>
    
    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>
    
    <style>
    .notifications-dropdown {
        width: 320px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    .dropdown-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }
    
    .notifications-count {
        background: #ef4444;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }
    
    .notifications-list {
        max-height: 280px;
        overflow-y: auto;
    }
    
    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    
    .notification-item:hover {
        background: #f9fafb;
    }
    
    .notification-item.unread {
        background: #fef3f2;
        border-right: 3px solid #ef4444;
    }
    
    .notification-item i {
        margin-left: 12px;
        margin-top: 2px;
        color: #6b7280;
        flex-shrink: 0;
    }
    
    .notification-item.unread i {
        color: #ef4444;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-content p {
        margin: 0 0 4px 0;
        font-size: 14px;
        color: #374151;
        line-height: 1.4;
    }
    
    .notification-time {
        font-size: 12px;
        color: #6b7280;
    }
    
    .dropdown-footer {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        text-align: center;
    }
    
    .view-all-notifications {
        color: #3b82f6;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }
    
    .view-all-notifications:hover {
        color: #2563eb;
    }
    
    /* Audio Recorder Styles */
    .audio-recorder {
        margin-top: 20px;
        padding: 20px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        text-align: center;
    }
    
    .recording-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #ef4444;
        font-weight: 500;
    }
    
    .pulse-dot {
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .recording-preview {
        margin-top: 15px;
    }
    
    .recording-actions {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .btn-success {
        background: #10b981;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-danger {
        background: #ef4444;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* File Upload Styles */
    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s;
    }
    
    .file-upload-area:hover {
        border-color: #3b82f6;
    }
    
    .upload-queue {
        margin-top: 20px;
    }
    
    .upload-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    
    .file-info small {
        color: #6b7280;
    }
    
    .upload-progress {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    
    .progress-bar {
        flex: 1;
        height: 8px;
        background: #f3f4f6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #10b981;
        transition: width 0.3s;
    }
    
    .progress-text {
        font-size: 12px;
        color: #6b7280;
        min-width: 35px;
    }
    
    .remove-file {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        padding: 5px;
    }
    </style>
    @stack('scripts')
</body>
</html>