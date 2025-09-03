<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'assigned_to_id' => $this->faker->randomElement([null, User::inRandomOrder()->where('role', '!=', RoleEnum::ADMIN->value())->first()?->id]),
        ];
    }
}
