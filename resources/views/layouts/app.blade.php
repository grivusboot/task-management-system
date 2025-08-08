<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Vue.js 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Sortable.js for drag & drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <!-- Pusher for real-time notifications -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        .notification-badge {
            animation: pulse-slow 2s infinite;
        }
        
        .kanban-column {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
        }
        
        .task-card {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }
        
        .priority-urgent {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        
        .priority-high {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }
        
        .priority-medium {
            background: linear-gradient(135deg, #eab308, #ca8a04);
        }
        
        .priority-low {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }
        
        .status-todo {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }
        
        .status-in-progress {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }
        
        .status-review {
            background: linear-gradient(135deg, #eab308, #ca8a04);
        }
        
        .status-completed {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }
    </style>
</head>
<body class="font-inter antialiased bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <div id="app" class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-lg border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    TaskFlow
                                </span>
                            </a>
                        </div>
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" 
                               class="nav-link {{ request()->routeIs('dashboard') ? 'active text-gray-900' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center px-3 py-2 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('projects.index') }}" 
                               class="nav-link {{ request()->routeIs('projects.*') ? 'active text-gray-900' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center px-3 py-2 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                Projects
                            </a>
                            <a href="{{ route('tasks.index') }}" 
                               class="nav-link {{ request()->routeIs('tasks.*') ? 'active text-gray-900' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center px-3 py-2 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                My Tasks
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notifications-button" class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/>
                                </svg>
                                <span id="notification-count" class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden notification-badge">
                                    0
                                </span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-gray-200/50 z-50">
                                <div class="p-4 border-b border-gray-200/50">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                        <button id="close-notifications" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div id="notifications-list" class="max-h-64 overflow-y-auto">
                                    <div class="p-4 text-center text-gray-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/>
                                        </svg>
                                        No notifications yet
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User menu -->
                        <div class="flex items-center space-x-3">
                            <div class="hidden sm:flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            </div>
                            
                            @if(Auth::user()->isApplicationAdmin())
                                <a href="{{ route('application.admin.dashboard') }}" class="text-sm text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    App Admin
                                </a>
                            @elseif(Auth::user()->isProjectAdmin())
                                <a href="{{ route('project.admin.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Project Admin
                                </a>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu -->
                <div class="sm:hidden hidden" id="mobile-menu">
                    <div class="pt-2 pb-3 space-y-1 bg-white/50 backdrop-blur-sm rounded-lg mt-2">
                        <a href="{{ route('dashboard') }}" 
                           class="{{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'text-gray-500 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                            Dashboard
                        </a>
                        <a href="{{ route('projects.index') }}" 
                           class="{{ request()->routeIs('projects.*') ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'text-gray-500 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                            Projects
                        </a>
                        <a href="{{ route('tasks.index') }}" 
                           class="{{ request()->routeIs('tasks.*') ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'text-gray-500 hover:bg-gray-50' }} block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                            My Tasks
                        </a>
                        @if(Auth::user()->isApplicationAdmin())
                            <a href="{{ route('application.admin.dashboard') }}" 
                               class="{{ request()->routeIs('application.admin.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white' : 'text-red-600 hover:bg-red-50' }} block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                                App Admin Panel
                            </a>
                        @elseif(Auth::user()->isProjectAdmin())
                            <a href="{{ route('project.admin.dashboard') }}" 
                               class="{{ request()->routeIs('project.admin.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'text-blue-600 hover:bg-blue-50' }} block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                                Project Admin Panel
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 animate-fade-in">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 animate-fade-in">
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Real-time notifications -->
    <script>
        // Initialize Pusher for real-time notifications
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('private-notifications.{{ Auth::id() }}');
        
        channel.bind('new-notification', function(data) {
            // Update notification count
            const countElement = document.getElementById('notification-count');
            const currentCount = parseInt(countElement.textContent) || 0;
            countElement.textContent = currentCount + 1;
            countElement.classList.remove('hidden');
            
            // Show notification toast
            showNotification(data.title, data.message);
            
            // Add notification to dropdown
            addNotificationToDropdown(data);
        });
        
        // Load existing notifications
        loadNotifications();
        
        function loadNotifications() {
            fetch('/notifications')
                .then(response => response.json())
                .then(notifications => {
                    const notificationsList = document.getElementById('notifications-list');
                    const countElement = document.getElementById('notification-count');
                    
                    if (notifications.length === 0) {
                        notificationsList.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/>
                                </svg>
                                No notifications yet
                            </div>
                        `;
                        countElement.classList.add('hidden');
                    } else {
                        const unreadCount = notifications.filter(n => !n.read_at).length;
                        if (unreadCount > 0) {
                            countElement.textContent = unreadCount;
                            countElement.classList.remove('hidden');
                        }
                        
                        notificationsList.innerHTML = notifications.map(notification => `
                            <div class="p-4 border-b border-gray-100 last:border-b-0 ${notification.read_at ? 'opacity-75' : ''}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                        <p class="text-sm text-gray-500 mt-1">${notification.message}</p>
                                        <p class="text-xs text-gray-400 mt-2">${new Date(notification.created_at).toLocaleString()}</p>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        ${!notification.read_at ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                                        <button onclick="deleteNotification(${notification.id})" class="text-gray-400 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }
        
        function addNotificationToDropdown(notification) {
            const notificationsList = document.getElementById('notifications-list');
            const emptyMessage = notificationsList.querySelector('.text-center');
            
            if (emptyMessage) {
                notificationsList.innerHTML = '';
            }
            
            const notificationElement = document.createElement('div');
            notificationElement.className = 'p-4 border-b border-gray-100 last:border-b-0';
            notificationElement.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                        <p class="text-sm text-gray-500 mt-1">${notification.message}</p>
                        <p class="text-xs text-gray-400 mt-2">${new Date(notification.created_at).toLocaleString()}</p>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <button onclick="deleteNotification(${notification.id})" class="text-gray-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            notificationsList.insertBefore(notificationElement, notificationsList.firstChild);
        }
        
        function deleteNotification(id) {
            fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error deleting notification:', error));
        }

        function showNotification(title, message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-white/90 backdrop-blur-lg border border-gray-200 rounded-lg shadow-lg p-4 z-50 animate-slide-up';
            toast.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="text-sm text-gray-500">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
        
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Notifications dropdown functionality
            const notificationsButton = document.getElementById('notifications-button');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const closeNotifications = document.getElementById('close-notifications');
            
            if (notificationsButton && notificationsDropdown) {
                notificationsButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationsDropdown.classList.toggle('hidden');
                });
                
                if (closeNotifications) {
                    closeNotifications.addEventListener('click', function() {
                        notificationsDropdown.classList.add('hidden');
                    });
                }
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!notificationsDropdown.contains(e.target) && !notificationsButton.contains(e.target)) {
                        notificationsDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
