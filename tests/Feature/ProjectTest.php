<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
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

    public function test_admin_can_create_project(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/projects', [
            'title' => 'Project',
            'description' => 'Project description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(rand(1, 30))->toDateString(),
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'Project',
        ]);
    }

    public function test_user_can_list_projects(): void
    {
        $this->authenticate();

        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_user_can_view_single_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $project->id,
                    'title' => $project->title,
                ]
            ]);
    }

    public function test_admin_can_update_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->putJson("/api/projects/{$project->id}", [
            'title' => 'Updated Project',
            'description' => 'Updated description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(rand(1, 30))->toDateString(),
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $project->id,
                    'title' => 'Updated Project',
                    'description' => 'Updated description',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project',
        ]);
    }

    public function test_admin_can_delete_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_member_cannot_create_project(): void
    {
        $this->authenticate(RoleEnum::MEMBER->value);

        $response = $this->postJson('/api/projects', [
            'title' => 'Project',
            'description' => 'Project description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(rand(1, 30))->toDateString(),
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('projects', [
            'title' => 'Project',
        ]);
    }

    public function test_member_cannot_update_project(): void
    {
        $this->authenticate(RoleEnum::MEMBER->value);

        $project = Project::factory()->create();

        $response = $this->putJson("/api/projects/{$project->id}", [
            'title' => 'Updated Project',
            'description' => 'Updated description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(rand(1, 30))->toDateString(),
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => $project->title,
        ]);
    }

    public function test_member_cannot_delete_project(): void
    {
        $this->authenticate(RoleEnum::MEMBER->value);

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);
    }
}
