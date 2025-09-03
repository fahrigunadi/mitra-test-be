<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate($role = 'admin'): User
    {
        $user = User::factory()->create([
            'role' => $role,
        ]);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_user_can_create_task_in_project(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();

        $response = $this->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'My Task',
            'description' => 'Task description',
            'status' => 'todo',
            'assigned_to_id' => User::factory()->create()->id,
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'status',
                    'project_id',
                    'assigned_to',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'My Task',
            'project_id' => $project->id,
        ]);
    }

    public function test_user_can_list_tasks_in_project(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();
        Task::factory()->count(3)->create(['project_id' => $project->id]);

        $response = $this->getJson("/api/projects/{$project->id}/tasks");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'status', 'project_id', 'assigned_to', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_user_can_view_single_task_in_project(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->getJson("/api/projects/{$project->id}/tasks/{$task->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    public function test_admin_can_update_task_in_project(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->putJson("/api/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'done',
            'assigned_to_id' => User::factory()->create()->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Task',
                    'status' => 'done',
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'done',
        ]);
    }

    public function test_member_cannot_update_task_in_project(): void
    {
        $this->authenticate(RoleEnum::MEMBER->value);
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id, 'assigned_to_id' => User::factory()->create()->id]);

        $response = $this->putJson("/api/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'done',
            'assigned_to_id' => User::factory()->create()->id,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
        ]);
    }

    public function test_member_can_update_task_in_project(): void
    {
        $user = $this->authenticate(RoleEnum::MEMBER->value);
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id, 'assigned_to_id' => $user->id]);

        $response = $this->putJson("/api/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'done',
            'assigned_to_id' => $user->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Task',
                    'status' => 'done',
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'done',
        ]);
    }

    public function test_admin_can_delete_task_in_project(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->deleteJson("/api/projects/{$project->id}/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_member_cannot_delete_task_in_project(): void
    {
        $this->authenticate(RoleEnum::MEMBER->value);
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->deleteJson("/api/projects/{$project->id}/tasks/{$task->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }
}
