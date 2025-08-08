@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-1"></i>Back to Users
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Admin Role -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_admin" 
                           value="1"
                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Admin privileges</span>
                </label>
                <p class="mt-1 text-xs text-gray-500">
                    Check this box to grant admin privileges to this user.
                </p>
            </div>

            <!-- Password (Optional) -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password (leave blank to keep current)
                </label>
                <input type="password" 
                       id="password" 
                       name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm New Password
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">User Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">User ID:</span>
                        <span class="font-medium">{{ $user->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Joined:</span>
                        <span class="font-medium">{{ $user->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Current Role:</span>
                        <span class="font-medium">
                            @if($user->isAdmin())
                                <span class="text-red-600">Admin</span>
                            @else
                                <span class="text-gray-600">User</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.users') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

