@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Here's what's happening with your projects and tasks.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Projects</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $projects->where('status', 'active')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Tasks</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $assignedTasks->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Tasks</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $assignedTasks->where('status', 'completed')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Notifications</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $notifications->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Projects -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Projects</h3>
                    <a href="{{ route('projects.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                        Create New
                    </a>
                </div>
                
                @if($projects->count() > 0)
                    <div class="space-y-4">
                        @foreach($projects->take(5) as $project)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $project->color }}"></div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $project->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $project->tasks->count() }} tasks</p>
                                    </div>
                                </div>
                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
                        <div class="mt-6">
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Create Project
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- My Tasks -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">My Tasks</h3>
                    <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                        View All
                    </a>
                </div>
                
                @if($assignedTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($assignedTasks->take(5) as $task)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @switch($task->priority)
                                            @case('urgent')
                                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                @break
                                            @case('high')
                                                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                                @break
                                            @case('medium')
                                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                                @break
                                            @default
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        @endswitch
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $task->project->name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($task->status === 'review') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any tasks assigned to you yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection



