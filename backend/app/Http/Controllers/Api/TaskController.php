<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request, int $projectId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        Gate::authorize('view', $project);

        $tasks = Task::where('project_id', $projectId)
            ->with(['assignedTo:id,name,email,avatar', 'creator:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->assigned_to === 'me', fn($q) => $q->where('assigned_to', $request->user()->id))
            ->when($request->assigned_to && $request->assigned_to !== 'me', fn($q) => $q->where('assigned_to', $request->assigned_to))
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $project = Project::findOrFail($request->project_id);
        
        // Verificar que el usuario es miembro del proyecto
        $isMember = $project->owner_id === $request->user()->id || 
                    $project->members()->where('user_id', $request->user()->id)->exists();
        
        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'No eres miembro de este proyecto',
            ], 403);
        }

        // Get max position for new task
        $maxPos = Task::where('project_id', $request->project_id)
            ->where('status', $request->status ?? 'todo')
            ->max('position');
        
        $position = $maxPos !== null ? $maxPos + 1 : 1;

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'todo',
            'priority' => $request->priority ?? 'medium',
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => $request->user()->id,
            'due_date' => $request->due_date,
            'position' => $position,
        ]);

        // Cargar relaciones después de crear
        $task->load(['assignedTo:id,name,avatar', 'creator:id,name', 'project:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada exitosamente',
            'data' => $task,
        ], 201);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $task->load([
            'assignedTo:id,name,email,avatar',
            'creator:id,name,email',
            'project:id,name,color'
        ]);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());
        $task->load(['assignedTo:id,name,avatar', 'creator:id,name', 'project:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada exitosamente',
            'data' => $task,
        ]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada exitosamente',
        ]);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('updateStatus', $task);

        $oldStatus = $task->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $maxPos = Task::where('project_id', $task->project_id)
                ->where('status', $newStatus)
                ->max('position');

            $task->position = $maxPos !== null ? $maxPos + 1 : 1;
        }

        $task->status = $newStatus;
        $task->save();

        $task->load(['assignedTo:id,name,avatar']);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'data' => $task,
        ]);
    }

    public function assign(AssignTaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('assign', $task);

        $project = $task->project;
        $isMember = $project->owner_id === $request->user_id ||
                    $project->members()->where('user_id', $request->user_id)->exists();

        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario debe ser miembro del proyecto',
            ], 403);
        }

        $task->assigned_to = $request->user_id;
        $task->save();

        $task->load(['assignedTo:id,name,email,avatar']);

        return response()->json([
            'success' => true,
            'message' => 'Tarea asignada exitosamente',
            'data' => $task,
        ]);
    }
}