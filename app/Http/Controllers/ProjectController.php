<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = $user->projects()->with('creator')->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'users' => 'array',
            'user_roles' => 'array',
        ]);

        DB::transaction(function () use ($request) {
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'created_by' => Auth::id(),
            ]);

            // Add creator as admin
            UserRole::create([
                'user_id' => Auth::id(),
                'project_id' => $project->id,
                'role' => 'admin',
            ]);

            // Add other users
            if ($request->users) {
                foreach ($request->users as $index => $userId) {
                    if ($userId) {
                        UserRole::create([
                            'user_id' => $userId,
                            'project_id' => $project->id,
                            'role' => $request->user_roles[$index] ?? 'user',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        
        $project->load(['tasks' => function($query) {
            $query->orderBy('order');
        }, 'tasks.assignee', 'tasks.subtasks', 'userRoles.user']);
        
        $users = User::all();
        
        return view('projects.show', compact('project', 'users'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);
        
        $users = User::all();
        $project->load('userRoles.user');
        
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'status' => 'required|in:active,completed,archived',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }

    public function addUser(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,user',
        ]);

        UserRole::create([
            'user_id' => $request->user_id,
            'project_id' => $project->id,
            'role' => $request->role,
        ]);

        return back()->with('success', 'User added to project successfully!');
    }

    public function removeUser(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        
        UserRole::where('user_id', $request->user_id)
            ->where('project_id', $project->id)
            ->delete();

        return back()->with('success', 'User removed from project successfully!');
    }
}
