<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all tasks that user has access to (assigned or from projects they're part of)
        $query = Task::whereHas('project.userRoles', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['project', 'assignee', 'subtasks', 'creator']);
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }
        
        $tasks = $query->orderBy('due_date')->paginate(15);
        
        // Get projects for filter dropdown
        $projects = $user->projects()->orderBy('name')->get();
        
        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create(Project $project)
    {
        Gate::authorize('view', $project);
        
        $users = User::all();
        return view('tasks.create', compact('project', 'users'));
    }

    public function store(Request $request, Project $project)
    {
        Gate::authorize('view', $project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'project_id' => $project->id,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
            'due_date' => $request->due_date,
            'order' => Task::where('project_id', $project->id)->where('status', $request->status)->max('order') + 1,
        ]);

        // Create notification for assigned user
        if ($request->assigned_to) {
            $notification = Notification::create([
                'user_id' => $request->assigned_to,
                'type' => 'task_assigned',
                'title' => 'New Task Assigned',
                'message' => "You have been assigned to task: {$task->title}",
                'data' => ['task_id' => $task->id, 'project_id' => $project->id],
            ]);
            
            // Broadcast notification in real-time
            broadcast(new NewNotification($notification))->toOthers();
        }

        return redirect()->route('projects.show', $project)->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task->project);
        
        $task->load(['project', 'assignee', 'subtasks', 'creator']);
        
        return view('tasks.show', compact('task'));
    }

    public function showModal(Task $task)
    {
        Gate::authorize('view', $task->project);
        
        $task->load(['project', 'assignee', 'subtasks', 'creator']);
        
        return response()->json($task);
    }

    public function getProjectTasks(Project $project)
    {
        Gate::authorize('view', $project);
        
        $tasks = $project->tasks()
            ->with(['assignee', 'subtasks', 'creator'])
            ->orderBy('order')
            ->get();
        
        return response()->json($tasks);
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task->project);
        
        $users = User::all();
        
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task->project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $oldAssignee = $task->assigned_to;
        
        $task->update($request->all());

        // Handle completion
        if ($request->status === 'completed' && !$task->completed_at) {
            $task->update(['completed_at' => now()]);
        } elseif ($request->status !== 'completed') {
            $task->update(['completed_at' => null]);
        }

        // Create notification for new assignee
        if ($request->assigned_to && $request->assigned_to != $oldAssignee) {
            $notification = Notification::create([
                'user_id' => $request->assigned_to,
                'type' => 'task_assigned',
                'title' => 'Task Assigned',
                'message' => "You have been assigned to task: {$task->title}",
                'data' => ['task_id' => $task->id, 'project_id' => $task->project_id],
            ]);
            
            // Broadcast notification in real-time
            broadcast(new NewNotification($notification))->toOthers();
        }

        return redirect()->route('projects.show', $task->project)->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task->project);
        
        $project = $task->project;
        $task->delete();
        
        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task deleted successfully!']);
        }
        
        return redirect()->route('projects.show', $project)->with('success', 'Task deleted successfully!');
    }

    // Drag & Drop functionality
    public function updateOrder(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.order' => 'required|integer',
            'tasks.*.status' => 'required|in:todo,in_progress,review,completed',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->tasks as $taskData) {
                $task = Task::find($taskData['id']);
                Gate::authorize('update', $task->project);
                
                $task->update([
                    'order' => $taskData['order'],
                    'status' => $taskData['status'],
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, Task $task)
    {
        Gate::authorize('update', $task->project);
        
        $request->validate([
            'status' => 'required|in:todo,in_progress,review,completed',
        ]);

        $task->update(['status' => $request->status]);

        if ($request->status === 'completed' && !$task->completed_at) {
            $task->update(['completed_at' => now()]);
        } elseif ($request->status !== 'completed') {
            $task->update(['completed_at' => null]);
        }

        return response()->json(['success' => true, 'task' => $task->load('assignee')]);
    }
}
