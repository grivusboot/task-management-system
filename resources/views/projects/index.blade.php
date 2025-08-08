@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Projects</h1>
                <p class="text-gray-600">Manage your projects and collaborate with your team</p>
            </div>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Project
            </a>
        </div>
    </div>

    <!-- Projects Grid -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Project Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $project->color }}"></div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                    <p class="text-sm text-gray-500">Created by {{ $project->creator->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($project->status === 'active') bg-green-100 text-green-800
                                    @elseif($project->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Project Description -->
                        @if($project->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $project->description }}</p>
                        @endif

                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $project->tasks->count() }}</div>
                                <div class="text-xs text-gray-500">Tasks</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $project->userRoles->count() }}</div>
                                <div class="text-xs text-gray-500">Members</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $totalTasks = $project->tasks->count();
                            $completedTasks = $project->tasks->where('status', 'completed')->count();
                            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $completedTasks }}/{{ $totalTasks }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            @if($project->start_date)
                                <span>Started: {{ $project->start_date->format('M j, Y') }}</span>
                            @endif
                            @if($project->due_date)
                                <span class="{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                    Due: {{ $project->due_date->format('M j, Y') }}
                                </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                View Project
                            </a>
                            <div class="flex items-center space-x-2">
                                @if($project->userRoles->where('user_id', Auth::id())->first()->role === 'admin')
                                    <a href="{{ route('projects.edit', $project) }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif
                                <span class="text-xs text-gray-400">
                                    {{ $project->userRoles->where('user_id', Auth::id())->first()->role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
            <div class="mt-6">
                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Project
                </a>
            </div>
        </div>
    @endif
</div>
@endsection



