@extends('admin.layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Project</h1>
        <a href="{{ route('admin.projects') }}" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-1"></i>Back to Projects
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.projects.update', $project) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Project Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $project->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       required>
                @error('name')
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select id="status" 
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                    <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>
                        On Hold
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Project Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Project ID:</span>
                        <span class="font-medium">{{ $project->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created By:</span>
                        <span class="font-medium">{{ $project->createdBy->name ?? 'Unknown' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium">{{ $project->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="font-medium">{{ $project->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Total Tasks:</span>
                        <span class="font-medium">{{ $project->tasks->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Current Status:</span>
                        <span class="font-medium">
                            @if($project->status === 'active')
                                <span class="text-green-600">Active</span>
                            @elseif($project->status === 'completed')
                                <span class="text-blue-600">Completed</span>
                            @elseif($project->status === 'on_hold')
                                <span class="text-yellow-600">On Hold</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.projects') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Update Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

