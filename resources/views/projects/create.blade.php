@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-500 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Project</h1>
                <p class="text-gray-600">Start a new project and invite team members</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('projects.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Project Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Color</label>
                    <div class="grid grid-cols-6 gap-3">
                        @php
                            $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1', '#14B8A6', '#F43F5E'];
                        @endphp
                        @foreach($colors as $color)
                            <label class="relative">
                                <input type="radio" name="color" value="{{ $color }}" {{ old('color', '#3B82F6') == $color ? 'checked' : '' }} class="sr-only">
                                <div class="w-10 h-10 rounded-lg border-2 cursor-pointer transition-all {{ old('color', '#3B82F6') == $color ? 'border-gray-900 scale-110' : 'border-gray-300 hover:border-gray-400' }}" style="background-color: {{ $color }}"></div>
                            </label>
                        @endforeach
                    </div>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Team Members -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Add Team Members</label>
                    <div id="team-members" class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <select name="users[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select a user</option>
                                @foreach($users as $user)
                                    @if($user->id != Auth::id())
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <select name="user_roles[]" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <button type="button" onclick="removeTeamMember(this)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addTeamMember()" class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Member
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('projects.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Project
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addTeamMember() {
    const container = document.getElementById('team-members');
    const newMember = document.createElement('div');
    newMember.className = 'flex items-center space-x-3';
    newMember.innerHTML = `
        <select name="users[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="">Select a user</option>
            @foreach($users as $user)
                @if($user->id != Auth::id())
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endif
            @endforeach
        </select>
        <select name="user_roles[]" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="button" onclick="removeTeamMember(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newMember);
}

function removeTeamMember(button) {
    button.parentElement.remove();
}

// Color selection functionality
document.querySelectorAll('input[name="color"]').forEach(input => {
    input.addEventListener('change', function() {
        document.querySelectorAll('input[name="color"]').forEach(radio => {
            const colorDiv = radio.nextElementSibling;
            colorDiv.classList.remove('border-gray-900', 'scale-110');
            colorDiv.classList.add('border-gray-300');
        });
        
        const selectedDiv = this.nextElementSibling;
        selectedDiv.classList.remove('border-gray-300');
        selectedDiv.classList.add('border-gray-900', 'scale-110');
    });
});
</script>
@endsection



