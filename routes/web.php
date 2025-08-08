<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationAdminController;
use App\Http\Controllers\ProjectAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication routes (public)
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Redirect root to login if not authenticated
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return app(DashboardController::class)->index();
})->name('dashboard');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    
    // Admin redirect based on type
    Route::get('/admin', function () {
        $user = auth()->user();
        if ($user->isApplicationAdmin()) {
            return redirect()->route('application.admin.dashboard');
        } elseif ($user->isProjectAdmin()) {
            return redirect()->route('project.admin.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    })->name('admin');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/add-user', [ProjectController::class, 'addUser'])->name('projects.add-user');
    Route::delete('projects/{project}/remove-user', [ProjectController::class, 'removeUser'])->name('projects.remove-user');
    
    // Tasks
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('projects/{project}/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}/modal', [TaskController::class, 'showModal'])->name('tasks.modal');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('projects/{project}/tasks', [TaskController::class, 'getProjectTasks'])->name('projects.tasks');
    
    // Task drag & drop
    Route::post('tasks/update-order', [TaskController::class, 'updateOrder'])->name('tasks.update-order');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    // Subtasks
    Route::post('tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::put('subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
    Route::delete('subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
    Route::patch('subtasks/{subtask}/toggle', [SubtaskController::class, 'toggleComplete'])->name('subtasks.toggle');
    Route::post('tasks/{task}/subtasks/update-order', [SubtaskController::class, 'updateOrder'])->name('subtasks.update-order');
    
    // Notifications
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::delete('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Application Admin routes (can manage all users and projects)
Route::middleware(['auth', 'application.admin'])->prefix('admin/application')->name('application.admin.')->group(function () {
    // Dashboard
    Route::get('/', [ApplicationAdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('users', [ApplicationAdminController::class, 'users'])->name('users');
    Route::get('users/{user}/edit', [ApplicationAdminController::class, 'editUser'])->name('users.edit');
    Route::put('users/{user}', [ApplicationAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{user}', [ApplicationAdminController::class, 'deleteUser'])->name('users.delete');
    
    // Project Management
    Route::get('projects', [ApplicationAdminController::class, 'projects'])->name('projects');
    Route::get('projects/create', [ApplicationAdminController::class, 'createProject'])->name('projects.create');
    Route::post('projects', [ApplicationAdminController::class, 'storeProject'])->name('projects.store');
    Route::get('projects/{project}/edit', [ApplicationAdminController::class, 'editProject'])->name('projects.edit');
    Route::put('projects/{project}', [ApplicationAdminController::class, 'updateProject'])->name('projects.update');
    Route::delete('projects/{project}', [ApplicationAdminController::class, 'deleteProject'])->name('projects.delete');
    
    // Task Management
    Route::get('tasks', [ApplicationAdminController::class, 'tasks'])->name('tasks');
    Route::get('tasks/{task}/edit', [ApplicationAdminController::class, 'editTask'])->name('tasks.edit');
    Route::put('tasks/{task}', [ApplicationAdminController::class, 'updateTask'])->name('tasks.update');
    Route::delete('tasks/{task}', [ApplicationAdminController::class, 'deleteTask'])->name('tasks.delete');
});

// Project Admin routes (can manage only their own projects)
Route::middleware(['auth', 'project.admin'])->prefix('admin/project')->name('project.admin.')->group(function () {
    // Dashboard
    Route::get('/', [ProjectAdminController::class, 'dashboard'])->name('dashboard');
    
    // Project Management (only own projects)
    Route::get('projects', [ProjectAdminController::class, 'projects'])->name('projects');
    Route::get('projects/create', [ProjectAdminController::class, 'createProject'])->name('projects.create');
    Route::post('projects', [ProjectAdminController::class, 'storeProject'])->name('projects.store');
    Route::get('projects/{project}/edit', [ProjectAdminController::class, 'editProject'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectAdminController::class, 'updateProject'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectAdminController::class, 'deleteProject'])->name('projects.delete');
    
    // Task Management (only tasks from own projects)
    Route::get('tasks', [ProjectAdminController::class, 'tasks'])->name('tasks');
    Route::get('tasks/{task}/edit', [ProjectAdminController::class, 'editTask'])->name('tasks.edit');
    Route::put('tasks/{task}', [ProjectAdminController::class, 'updateTask'])->name('tasks.update');
    Route::delete('tasks/{task}', [ProjectAdminController::class, 'deleteTask'])->name('tasks.delete');
});

// Legacy admin routes (for backward compatibility)
Route::middleware(['auth', 'admin'])->prefix('admin/legacy')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Project Management
    Route::get('projects', [AdminController::class, 'projects'])->name('projects');
    Route::get('projects/{project}/edit', [AdminController::class, 'editProject'])->name('projects.edit');
    Route::put('projects/{project}', [AdminController::class, 'updateProject'])->name('projects.update');
    Route::delete('projects/{project}', [AdminController::class, 'deleteProject'])->name('projects.delete');
    
    // Task Management
    Route::get('tasks', [AdminController::class, 'tasks'])->name('tasks');
    Route::get('tasks/{task}/edit', [AdminController::class, 'editTask'])->name('tasks.edit');
    Route::put('tasks/{task}', [AdminController::class, 'updateTask'])->name('tasks.update');
    Route::delete('tasks/{task}', [AdminController::class, 'deleteTask'])->name('tasks.delete');
});
