@extends('admin.layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Task</h1>
        <a href="{{ route('admin.tasks') }}" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-1"></i>Back to Tasks
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.tasks.update', $task) }}">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Task Title
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $task->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project -->
            <div class="mb-4">
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Project
                </label>
                <select id="project_id" 
                        name="project_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        required>
                    <option value="">Select a project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assigned To -->
            <div class="mb-4">
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                    Assigned To
                </label>
                <select id="assigned_to" 
                        name="assigned_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select id="status" 
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        required>
                    <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>
                        In Progress
                    </option>
                    <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                    <option value="cancelled" {{ old('status', $task->status) === 'cancelled' ? 'selected' : '' }}>
                        Cancelled
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority -->
            <div class="mb-4">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    Priority
                </label>
                <select id="priority" 
                        name="priority"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        required>
                    <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>
                        Low
                    </option>
                    <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>
                        Medium
                    </option>
                    <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>
                        High
                    </option>
                    <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>
                        Urgent
                    </option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="mb-6">
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Due Date
                </label>
                <input type="date" 
                       id="due_date" 
                       name="due_date" 
                       value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Task Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Task Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Task ID:</span>
                        <span class="font-medium">{{ $task->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created By:</span>
                        <span class="font-medium">{{ $task->createdBy->name ?? 'Unknown' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium">{{ $task->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="font-medium">{{ $task->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Subtasks:</span>
                        <span class="font-medium">{{ $task->subtasks->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Current Status:</span>
                        <span class="font-medium">
                            @if($task->status === 'pending')
                                <span class="text-gray-600">Pending</span>
                            @elseif($task->status === 'in_progress')
                                <span class="text-blue-600">In Progress</span>
                            @elseif($task->status === 'completed')
                                <span class="text-green-600">Completed</span>
                            @elseif($task->status === 'cancelled')
                                <span class="text-red-600">Cancelled</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.tasks') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Update Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

