<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function test_user_can_view_authenticated(): void
    {
        $this->authenticate();

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_can_list_users(): void
    {
        $this->authenticate();

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                ],
            ]);
    }
}