<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        // Member of the project
        $project = $task->project;
        return $project->owner_id === $user->id || 
               $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        // Creator, assigned user, or project admin
        $project = $task->project;
        
        return $task->created_by === $user->id ||
               $task->assigned_to === $user->id ||
               $project->owner_id === $user->id ||
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        // Creator or project admin
        $project = $task->project;
        
        return $task->created_by === $user->id ||
               $project->owner_id === $user->id ||
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function updateStatus(User $user, Task $task): bool
    {
        // Assigned user or project admin
        $project = $task->project;
        
        return $task->assigned_to === $user->id ||
               $project->owner_id === $user->id ||
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function assign(User $user, Task $task): bool
    {
        // Project admin or owner
        $project = $task->project;
        
        return $project->owner_id === $user->id ||
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }
}