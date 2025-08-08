<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-red-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">
                        <i class="fas fa-shield-alt mr-2"></i>Admin Panel
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="hover:text-red-200">
                        <i class="fas fa-home mr-1"></i>Main App
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-200">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar and Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg min-h-screen">
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-red-50 text-red-600' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    
                    <a href="{{ route('admin.projects') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg {{ request()->routeIs('admin.projects*') ? 'bg-red-50 text-red-600' : '' }}">
                        <i class="fas fa-project-diagram mr-3"></i>
                        Projects
                    </a>
                    
                    <a href="{{ route('admin.tasks') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg {{ request()->routeIs('admin.tasks*') ? 'bg-red-50 text-red-600' : '' }}">
                        <i class="fas fa-tasks mr-3"></i>
                        Tasks
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>

