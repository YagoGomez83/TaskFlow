<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        // User is owner or member of the project
        return $project->owner_id === $user->id || 
               $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        // Owner or project admin can update
        return $project->owner_id === $user->id || 
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        // Only owner can delete
        return $project->owner_id === $user->id;
    }

    public function manageMembers(User $user, Project $project): bool
    {
        // Owner or project admin
        return $project->owner_id === $user->id || 
               $project->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }
}