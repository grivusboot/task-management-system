<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get user's projects through user roles
        $projects = Project::whereHas('userRoles', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['tasks' => function($query) {
            $query->orderBy('order');
        }])->get();
        
        // Get user's assigned tasks
        $assignedTasks = $user->assignedTasks()
            ->with(['project', 'subtasks'])
            ->orderBy('due_date')
            ->get();
        
        // Get recent notifications
        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact('projects', 'assignedTasks', 'notifications'));
    }
}
