<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'admin_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Task Management Relationships
    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedSubtasks(): HasMany
    {
        return $this->hasMany(Subtask::class, 'assigned_to');
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'user_roles');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->unread();
    }

    /**
     * Check if the user is an application admin (can manage all users and projects)
     */
    public function isApplicationAdmin(): bool
    {
        return $this->admin_type === 'application' || $this->is_admin === true;
    }

    /**
     * Check if the user is a project admin (can manage only their own projects)
     */
    public function isProjectAdmin(): bool
    {
        return $this->admin_type === 'project';
    }

    /**
     * Check if the user is any type of admin
     */
    public function isAdmin(): bool
    {
        return $this->isApplicationAdmin() || $this->isProjectAdmin();
    }

    /**
     * Check if the user can manage a specific project
     */
    public function canManageProject(Project $project): bool
    {
        if ($this->isApplicationAdmin()) {
            return true;
        }

        if ($this->isProjectAdmin()) {
            return $project->created_by === $this->id;
        }

        return false;
    }

    /**
     * Check if the user can manage a specific user
     */
    public function canManageUser(User $user): bool
    {
        if ($this->isApplicationAdmin()) {
            return $this->id !== $user->id; // Cannot manage own account
        }

        return false;
    }
}
