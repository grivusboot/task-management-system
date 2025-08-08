@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('projects.show', $task->project) }}" class="text-indigo-600 hover:text-indigo-500 mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                    <p class="text-gray-600">Task in {{ $task->project->name }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Task Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Task Details</h3>
                </div>
                <div class="p-6">
                    @if($task->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
                            <p class="text-gray-600">{{ $task->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Status</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status === 'review') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Priority</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($task->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>

                        @if($task->due_date)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Due Date</h4>
                                <p class="text-gray-600 {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                    {{ $task->due_date->format('M j, Y') }}
                                </p>
                            </div>
                        @endif

                        @if($task->assignee)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Assigned To</h4>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-xs font-medium text-white">{{ substr($task->assignee->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-gray-600">{{ $task->assignee->name }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($task->completed_at)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Completed</h4>
                            <p class="text-gray-600">{{ $task->completed_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Project Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Project</h3>
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $task->project->color }}"></div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $task->project->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $task->project->status }}</p>
                    </div>
                </div>
            </div>

            <!-- Created By -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Created By</h3>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                        <span class="text-sm font-medium text-white">{{ substr($task->creator->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $task->creator->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $task->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtasks -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Subtasks</h3>
                    <button onclick="openSubtaskModal()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Subtask
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if($task->subtasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($task->subtasks->sortBy('order') as $subtask)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ $subtask->is_completed ? 'checked' : '' }}
                                           onchange="toggleSubtask({{ $subtask->id }})">
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900 {{ $subtask->is_completed ? 'line-through text-gray-500' : '' }}">
                                            {{ $subtask->title }}
                                        </h4>
                                        @if($subtask->description)
                                            <p class="text-sm text-gray-500">{{ $subtask->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($subtask->assignee)
                                        <div class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">{{ substr($subtask->assignee->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <button onclick="deleteSubtask({{ $subtask->id }})" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subtasks</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a subtask.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleSubtask(subtaskId) {
    fetch(`/subtasks/${subtaskId}/toggle`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteSubtask(subtaskId) {
    if (confirm('Are you sure you want to delete this subtask?')) {
        fetch(`/subtasks/${subtaskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

function openSubtaskModal() {
    // This would open a modal to add a new subtask
    // For now, we'll redirect to a simple form
    window.location.href = `/tasks/${taskId}/subtasks/create`;
}
</script>
@endsection



