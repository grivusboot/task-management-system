@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <div class="text-sm text-gray-500">
            Welcome back, {{ auth()->user()->name }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-project-diagram text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Projects</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_projects'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_tasks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
            </div>
            <div class="p-6">
                @if($stats['recent_users']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_users'] as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No users found.</p>
                @endif
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Projects</h3>
            </div>
            <div class="p-6">
                @if($stats['recent_projects']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_projects'] as $project)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-project-diagram text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $project->status }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No projects found.</p>
                @endif
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
            </div>
            <div class="p-6">
                @if($stats['recent_tasks']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_tasks'] as $task)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-tasks text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $task->status }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $task->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No tasks found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                <span class="text-sm font-medium text-gray-900">Manage Users</span>
            </a>
            <a href="{{ route('admin.projects') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fas fa-project-diagram text-green-600 mr-3"></i>
                <span class="text-sm font-medium text-gray-900">Manage Projects</span>
            </a>
            <a href="{{ route('admin.tasks') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fas fa-tasks text-purple-600 mr-3"></i>
                <span class="text-sm font-medium text-gray-900">Manage Tasks</span>
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fas fa-home text-gray-600 mr-3"></i>
                <span class="text-sm font-medium text-gray-900">Go to Main App</span>
            </a>
        </div>
    </div>
</div>
@endsection

