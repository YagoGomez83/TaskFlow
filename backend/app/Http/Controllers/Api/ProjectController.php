<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMemberRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get projects owned by user or where user is member
        $projects = Project::where('owner_id', $user->id)
            ->orWhereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->with(['owner:id,name,email', 'members:id,name,email,avatar'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#3B82F6',
            'status' => $request->status ?? 'active',
            'owner_id' => $request->user()->id,
        ]);

        $project->load(['owner:id,name,email', 'members']);

        return response()->json([
            'success' => true,
            'message' => 'Proyecto creado exitosamente',
            'data' => $project,
        ], 201);
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $project->load([
            'owner:id,name,email,avatar',
            'members:id,name,email,avatar',
            'tasks' => fn($q) => $q->with('assignedTo:id,name,avatar')->orderBy('position')
        ]);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());
        $project->load(['owner:id,name,email', 'members']);

        return response()->json([
            'success' => true,
            'message' => 'Proyecto actualizado exitosamente',
            'data' => $project,
        ]);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proyecto eliminado exitosamente',
        ]);
    }

    public function addMember(AddMemberRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('manageMembers', $project);

        // Check if user is already a member
        if ($project->members()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya es miembro del proyecto',
            ], 409);
        }

        $project->members()->attach($request->user_id, [
            'role' => $request->role ?? 'member',
        ]);

        $project->load('members:id,name,email,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Miembro agregado exitosamente',
            'data' => $project->members,
        ]);
    }

    public function removeMember(Request $request, Project $project, int $userId): JsonResponse
    {
        Gate::authorize('manageMembers', $project);

        // Cannot remove owner
        if ($project->owner_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede remover al propietario del proyecto',
            ], 403);
        }

        $project->members()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Miembro removido exitosamente',
        ]);
    }
}