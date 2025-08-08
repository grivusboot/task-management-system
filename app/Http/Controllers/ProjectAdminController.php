<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectAdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $stats = [
            'total_projects' => $user->createdProjects()->count(),
            'total_tasks' => $user->createdProjects()->withCount('tasks')->get()->sum('tasks_count'),
            'recent_projects' => $user->createdProjects()->latest()->take(5)->get(),
            'recent_tasks' => Task::whereIn('project_id', $user->createdProjects()->pluck('id'))->latest()->take(5)->get(),
        ];

        return view('admin.project.dashboard', compact('stats'));
    }

    // Project Management (only own projects)
    public function projects()
    {
        $projects = auth()->user()->createdProjects()->latest()->paginate(10);
        return view('admin.project.projects.index', compact('projects'));
    }

    public function createProject()
    {
        return view('admin.project.projects.create');
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

        return redirect()->route('project.admin.projects')->with('success', 'Project created successfully.');
    }

    public function editProject(Project $project)
    {
        if (!auth()->user()->canManageProject($project)) {
            abort(403, 'You can only edit your own projects.');
        }

        return view('admin.project.projects.edit', compact('project'));
    }

    public function updateProject(Request $request, Project $project)
    {
        if (!auth()->user()->canManageProject($project)) {
            abort(403, 'You can only edit your own projects.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        $project->update($request->only(['name', 'description', 'status']));

        return redirect()->route('project.admin.projects')->with('success', 'Project updated successfully.');
    }

    public function deleteProject(Project $project)
    {
        if (!auth()->user()->canManageProject($project)) {
            abort(403, 'You can only delete your own projects.');
        }

        $project->delete();
        return redirect()->route('project.admin.projects')->with('success', 'Project deleted successfully.');
    }

    // Task Management (only tasks from own projects)
    public function tasks()
    {
        $user = auth()->user();
        $tasks = Task::whereIn('project_id', $user->createdProjects()->pluck('id'))
                    ->with(['project', 'assignee', 'creator'])
                    ->latest()
                    ->paginate(10);
        
        return view('admin.project.tasks.index', compact('tasks'));
    }

    public function editTask(Task $task)
    {
        if (!auth()->user()->canManageProject($task->project)) {
            abort(403, 'You can only edit tasks from your own projects.');
        }

        $projects = auth()->user()->createdProjects;
        $users = \App\Models\User::all();
        
        return view('admin.project.tasks.edit', compact('task', 'projects', 'users'));
    }

    public function updateTask(Request $request, Task $task)
    {
        if (!auth()->user()->canManageProject($task->project)) {
            abort(403, 'You can only edit tasks from your own projects.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        // Ensure the project_id is one of the user's projects
        if (!auth()->user()->createdProjects()->where('id', $request->project_id)->exists()) {
            abort(403, 'You can only assign tasks to your own projects.');
        }

        $task->update($request->only([
            'title', 'description', 'status', 'priority', 
            'project_id', 'assigned_to', 'due_date'
        ]));

        return redirect()->route('project.admin.tasks')->with('success', 'Task updated successfully.');
    }

    public function deleteTask(Task $task)
    {
        if (!auth()->user()->canManageProject($task->project)) {
            abort(403, 'You can only delete tasks from your own projects.');
        }

        $task->delete();
        return redirect()->route('project.admin.tasks')->with('success', 'Task deleted successfully.');
    }
}
