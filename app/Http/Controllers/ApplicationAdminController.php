<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApplicationAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_projects' => Project::latest()->take(5)->get(),
            'recent_tasks' => Task::latest()->take(5)->get(),
        ];

        return view('admin.application.dashboard', compact('stats'));
    }

    // User Management
    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.application.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.application.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'admin_type' => 'required|in:none,application,project',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'admin_type' => $request->admin_type,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('application.admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('application.admin.users')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('application.admin.users')->with('success', 'User deleted successfully.');
    }

    // Project Management
    public function projects()
    {
        $projects = Project::with('createdBy')->latest()->paginate(10);
        return view('admin.application.projects.index', compact('projects'));
    }

    public function createProject()
    {
        return view('admin.application.projects.create');
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('application.admin.projects')->with('success', 'Project created successfully.');
    }

    public function editProject(Project $project)
    {
        return view('admin.application.projects.edit', compact('project'));
    }

    public function updateProject(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        $project->update($request->only(['name', 'description', 'status']));

        return redirect()->route('application.admin.projects')->with('success', 'Project updated successfully.');
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        return redirect()->route('application.admin.projects')->with('success', 'Project deleted successfully.');
    }

    // Task Management
    public function tasks()
    {
        $tasks = Task::with(['project', 'assignee', 'creator'])->latest()->paginate(10);
        return view('admin.application.tasks.index', compact('tasks'));
    }

    public function editTask(Task $task)
    {
        $projects = Project::all();
        $users = User::all();
        return view('admin.application.tasks.edit', compact('task', 'projects', 'users'));
    }

    public function updateTask(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->only([
            'title', 'description', 'status', 'priority', 
            'project_id', 'assigned_to', 'due_date'
        ]));

        return redirect()->route('application.admin.tasks')->with('success', 'Task updated successfully.');
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
        return redirect()->route('application.admin.tasks')->with('success', 'Task deleted successfully.');
    }
}
