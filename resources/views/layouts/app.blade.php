<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StockFlow') — Stock Flow</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        // Theme flicker prevention
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen font-sans antialiased text-text-primary relative overflow-x-hidden">
    <!-- Ambient background glows -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-[260px] w-[600px] h-[600px] bg-primary/5 rounded-full blur-[140px] pointer-events-none -z-10"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-[260px] bg-sidebar border-r border-slate-800/40 flex flex-col fixed h-full z-40 transition-all duration-300 lg:translate-x-0 -translate-x-full">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 pt-8 pb-6 border-b border-slate-800/40">
                <div class="w-10 h-10 bg-gradient-to-tr from-primary to-violet-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30 text-white">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        <path d="M12 22V12" stroke="#34D399" stroke-width="3"/><path d="M12 12l4-4" stroke="#34D399" stroke-width="3"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-extrabold text-lg leading-tight tracking-tight">Stock Flow</h1>
                    <p class="text-text-secondary/70 text-[11px] font-medium uppercase tracking-widest">Business Manager</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 space-y-1.5 px-3">
                <p class="text-slate-500 text-[10px] font-extrabold tracking-wider px-3 mb-3 uppercase">Menu</p>
                <a href="{{ url('/products') }}" class="sidebar-link {{ request()->is('products*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Products</span>
                </a>
                <a href="{{ url('/customers') }}" class="sidebar-link {{ request()->is('customers*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Customers</span>
                </a>
                <a href="{{ url('/suppliers') }}" class="sidebar-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Suppliers</span>
                </a>
                <a href="{{ url('/sales') }}" class="sidebar-link {{ request()->is('sales*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Sales</span>
                </a>
                <a href="{{ url('/expenses') }}" class="sidebar-link {{ request()->is('expenses*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Expenses</span>
                </a>
                <a href="{{ url('/reminders') }}" class="sidebar-link {{ request()->is('reminders*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Reminders</span>
                </a>
                <!-- Admin-only links — shown via Blade for secure server-side check -->
                <div id="sidebar-admin-link" style="display: none;">
                    <a href="{{ url('/reports') }}" class="sidebar-link {{ request()->is('reports*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Reports</span>
                    </a>
                    <a href="{{ url('/users') }}" class="sidebar-link {{ request()->is('users*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Manage Users</span>
                    </a>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="px-4 pb-6 mt-auto">
                <div class="bg-sidebar-hover/40 border border-slate-800/60 rounded-2xl p-4 mb-4 flex items-center gap-3">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <div>
                        <p class="text-white text-xs font-semibold">Online Mode</p>
                        <p class="text-text-secondary text-[10px] mt-0.5">DB Connected</p>
                    </div>
                </div>
                <a href="{{ url('/profile') }}" class="w-full flex items-center gap-3 py-2.5 px-4 mb-3 bg-slate-700/30 hover:bg-slate-700/50 text-slate-300 hover:text-white font-semibold text-xs rounded-xl border border-slate-700/40 hover:border-slate-600/50 transition-all duration-200 cursor-pointer">
                    <img id="sidebar-user-photo" class="h-7 w-7 rounded-lg object-cover ring-1 ring-slate-600" src="" alt="Profile" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Ccircle cx=\'12\' cy=\'8\' r=\'4\' fill=\'%2394A3B8\'/%3E%3Cpath d=\'M4 20c0-4 3.6-7 8-7s8 3 8 7\' fill=\'%2394A3B8\'/%3E%3C/svg%3E'">
                    <div class="flex-1 min-w-0">
                        <p id="sidebar-username" class="text-xs font-semibold truncate">...</p>
                        <p class="text-[10px] text-slate-400">My Profile</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <button id="logoutBtn" onclick="logoutUser()" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-semibold text-xs rounded-xl border border-rose-500/20 hover:border-rose-500/30 transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Log Out</span>
                </button>
            </div>
        </aside>

        <!-- Mobile overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-[260px] p-6 lg:p-10">
            <!-- Mobile header -->
            <div class="lg:hidden flex items-center justify-between mb-6 bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-text-primary hover:bg-slate-50 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="font-extrabold text-text-primary tracking-tight">Stock Flow</span>
                </div>
            </div>

            <!-- Global Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white/70 backdrop-blur-md border border-slate-200/60 rounded-3xl p-5 md:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                <div class="flex items-center gap-4">
                    <div class="relative w-12 h-12 flex-shrink-0">
                        <img id="header-user-photo" class="h-12 w-12 rounded-2xl object-cover ring-2 ring-primary/20 bg-slate-100 shadow-sm" src="" alt="Profile" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Ccircle cx=\'12\' cy=\'8\' r=\'4\' fill=\'%2394A3B8\'/%3E%3Cpath d=\'M4 20c0-4 3.6-7 8-7s8 3 8 7\' fill=\'%2394A3B8\'/%3E%3C/svg%3E'">
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-800">
                            <span id="dynamic-greeting" class="text-indigo-600">Hello</span>, <span id="header-username" class="text-slate-800 font-extrabold">...</span>!
                        </h2>
                        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5 font-semibold">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span id="current-date-string">...</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-xl transition-all duration-200 shadow-sm border border-slate-200/40 dark:border-slate-700/40 flex items-center justify-center shrink-0 cursor-pointer" aria-label="Toggle dark mode">
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
                    </button>
                    <div class="text-right hidden sm:block">
                        <p id="header-user-fullname" class="text-xs font-bold text-slate-800">...</p>
                        <span id="header-user-role-badge" class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-md border border-indigo-100 mt-0.5">User</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200/80 hidden sm:block"></div>
                    <div id="header-admin-badge" style="display: none;">
                        <span id="header-portal-badge" class="text-xs font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Admin Portal
                        </span>
                    </div>
                    <div id="header-user-badge" style="display: none;">
                        <span id="header-portal-badge" class="text-xs font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Staff Portal
                        </span>
                    </div>
                </div>
            </div>

            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function updateHeaderGreeting() {
            const greetingEl = document.getElementById('dynamic-greeting');
            const dateEl = document.getElementById('current-date-string');
            const now = new Date();
            const hour = now.getHours();
            let greeting = hour >= 5 && hour < 12 ? 'Good morning'
                : hour >= 12 && hour < 17 ? 'Good afternoon'
                : hour >= 17 && hour < 22 ? 'Good evening'
                : 'Good night';
            if (greetingEl) greetingEl.textContent = greeting;
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        }
        updateHeaderGreeting();

        // Clean, server-rendered DOM ready state
        document.addEventListener('DOMContentLoaded', function() {
            updateHeaderGreeting();

            // Theme toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            if (themeToggleBtn && darkIcon && lightIcon) {
                if (document.documentElement.classList.contains('dark')) {
                    lightIcon.classList.remove('hidden');
                } else {
                    darkIcon.classList.remove('hidden');
                }
                themeToggleBtn.addEventListener('click', function() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                        lightIcon.classList.add('hidden');
                        darkIcon.classList.remove('hidden');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                        darkIcon.classList.add('hidden');
                        lightIcon.classList.remove('hidden');
                    }
                });
            }
        });
    </script>

    @stack('scripts')

    <script>
        // Populate user details from localStorage
        function populateUserDetails() {
            const user = getCurrentUser();
            if (!user) {
                // Not strictly necessary since EnsureApiAuthenticated will block, but good fallback
                window.location.href = '/login';
                return;
            }

            // Update Header
            const headerUsername = document.getElementById('header-username');
            const headerUserPhoto = document.getElementById('header-user-photo');
            const headerUserFullname = document.getElementById('header-user-fullname');
            const headerUserRoleBadge = document.getElementById('header-user-role-badge');
            
            if (headerUsername) headerUsername.textContent = user.username;
            if (headerUserPhoto) headerUserPhoto.src = user.profile_photo_url || '';
            if (headerUserFullname) headerUserFullname.textContent = user.name || user.username;
            if (headerUserRoleBadge) headerUserRoleBadge.textContent = user.role;

            // Show admin/user badges and elements
            if (user.role === 'Admin') {
                const adminBadge = document.getElementById('header-admin-badge');
                if (adminBadge) adminBadge.style.display = 'block';
                
                const sidebarAdminLink = document.getElementById('sidebar-admin-link');
                if (sidebarAdminLink) sidebarAdminLink.style.display = 'block';

                document.querySelectorAll('.admin-only').forEach(el => {
                    // if it's a table cell (th/td) we can display as table-cell, otherwise block or inline-flex
                    if (el.tagName === 'TH' || el.tagName === 'TD') {
                        el.style.display = 'table-cell';
                    } else if (el.classList.contains('flex') || el.classList.contains('inline-flex')) {
                        el.style.display = 'flex';
                    } else {
                        el.style.display = 'block';
                    }
                });
            } else {
                const userBadge = document.getElementById('header-user-badge');
                if (userBadge) userBadge.style.display = 'block';
            }

            // Update Sidebar
            const sidebarUsername = document.getElementById('sidebar-username');
            const sidebarUserPhoto = document.getElementById('sidebar-user-photo');
            if (sidebarUsername) sidebarUsername.textContent = user.username;
            if (sidebarUserPhoto) sidebarUserPhoto.src = user.profile_photo_url || '';

            // Refresh user details from API to keep localStorage and UI in sync
            if (window.apiRequest) {
                apiRequest('GET', '/user').then(res => {
                    if (res && res.success && res.data) {
                        const freshUser = {
                            id: res.data.id,
                            username: res.data.username,
                            role: res.data.role,
                            profile_photo_url: res.data.profile_photo_url,
                            name: res.data.name || res.data.username
                        };
                        localStorage.setItem('api_user', JSON.stringify(freshUser));
                        
                        // Update photo and metadata in DOM if changed
                        if (headerUserPhoto && freshUser.profile_photo_url && headerUserPhoto.src !== freshUser.profile_photo_url) {
                            headerUserPhoto.src = freshUser.profile_photo_url;
                        }
                        if (sidebarUserPhoto && freshUser.profile_photo_url && sidebarUserPhoto.src !== freshUser.profile_photo_url) {
                            sidebarUserPhoto.src = freshUser.profile_photo_url;
                        }
                        if (headerUserFullname && freshUser.name && headerUserFullname.textContent !== freshUser.name) {
                            headerUserFullname.textContent = freshUser.name;
                        }
                    }
                }).catch(err => console.error("Failed to refresh user details:", err));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            populateUserDetails();
        });
    </script>
</body>
</html>
