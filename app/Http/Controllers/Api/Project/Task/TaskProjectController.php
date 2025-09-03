<?php

namespace App\Http\Controllers\Api\Project\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $tasks = $project->tasks()
            ->latest('created_at')
            ->search($request->search)
            ->filter($request->status, $request->assigned_to_id)
            ->paginate($request->input('per_page', 10))
            ->onEachSide(1)
            ->withQueryString();

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request, Project $project): TaskResource
    {
        $task = $project->tasks()->create($request->validated());

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Project $project, Task $task): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task): Response
    {
        Gate::authorize('role-admin');

        $task->delete();

        return response()->noContent();
    }
}
