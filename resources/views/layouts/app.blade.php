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
    <script src="{{ asset('js/lucide.js') }}"></script>
    
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
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        @auth
        <!-- Simple Bottom Navigation -->
        <nav class="modern-bottom-nav">
            <div class="nav-container">
                <a href="{{ route('dashboard') }}" class="modern-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="home"></i>
                    <span class="nav-label">الرئيسية</span>
                </a>
                
                <a href="#" class="modern-nav-item">
                    <i data-lucide="bar-chart-3"></i>
                    <span class="nav-label">التقارير</span>
                </a>
                
                <a href="#" class="modern-nav-item">
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
            
            // User menu toggle
            const userMenu = document.getElementById('userMenu');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userMenu && userDropdown) {
                userMenu.addEventListener('click', function() {
                    userDropdown.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target)) {
                        userDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>