<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        Gate::authorize('update', $task->project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $subtask = Subtask::create([
            'title' => $request->title,
            'description' => $request->description,
            'task_id' => $task->id,
            'assigned_to' => $request->assigned_to,
            'order' => Subtask::where('task_id', $task->id)->max('order') + 1,
            'due_date' => $request->due_date,
        ]);

        // Create notification for assigned user
        if ($request->assigned_to && $request->assigned_to != Auth::id()) {
            Notification::create([
                'user_id' => $request->assigned_to,
                'type' => 'subtask_assigned',
                'title' => 'New Subtask Assigned',
                'message' => "You have been assigned to subtask: {$subtask->title}",
                'data' => ['subtask_id' => $subtask->id, 'task_id' => $task->id],
            ]);
        }

        return back()->with('success', 'Subtask created successfully!');
    }

    public function update(Request $request, Subtask $subtask)
    {
        Gate::authorize('update', $subtask->task->project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $oldAssignee = $subtask->assigned_to;
        
        $subtask->update($request->all());

        // Create notification for new assignee
        if ($request->assigned_to && $request->assigned_to != $oldAssignee && $request->assigned_to != Auth::id()) {
            Notification::create([
                'user_id' => $request->assigned_to,
                'type' => 'subtask_assigned',
                'title' => 'Subtask Assigned',
                'message' => "You have been assigned to subtask: {$subtask->title}",
                'data' => ['subtask_id' => $subtask->id, 'task_id' => $subtask->task_id],
            ]);
        }

        return back()->with('success', 'Subtask updated successfully!');
    }

    public function destroy(Subtask $subtask)
    {
        Gate::authorize('delete', $subtask->task->project);
        
        $subtask->delete();
        
        return back()->with('success', 'Subtask deleted successfully!');
    }

    public function toggleComplete(Subtask $subtask)
    {
        Gate::authorize('update', $subtask->task->project);
        
        $subtask->update([
            'is_completed' => !$subtask->is_completed,
            'completed_at' => !$subtask->is_completed ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'is_completed' => $subtask->is_completed,
        ]);
    }

    public function updateOrder(Request $request, Task $task)
    {
        Gate::authorize('update', $task->project);
        
        $request->validate([
            'subtasks' => 'required|array',
            'subtasks.*.id' => 'required|exists:subtasks,id',
            'subtasks.*.order' => 'required|integer',
        ]);

        foreach ($request->subtasks as $subtaskData) {
            Subtask::where('id', $subtaskData['id'])
                ->where('task_id', $task->id)
                ->update(['order' => $subtaskData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
