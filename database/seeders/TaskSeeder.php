<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::all()->each(function (Project $project) {
            $project->tasks()->saveMany(
                Task::factory(3)->make([
                    'project_id' => $project->id,
                    'assigned_to_id' => User::factory()->create()->id
                ])
            );
        });
    }
}
