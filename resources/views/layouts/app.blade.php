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
    @include('components.common-scripts')
    @include('components.delete-modal-script')
    @auth
    @include('components.player-id-sync')
    @endauth
    

    @stack('scripts')
</body>
</html>