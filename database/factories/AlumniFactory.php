<?php

namespace Database\Factories;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumni>
 */
class AlumniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'program' => fake()->randomElement(['BSIT', 'BSHM', 'BSN', 'BSMLS', 'BSBIO']),
            'graduation_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'current_employer' => fake()->optional(0.7)->company(),
            'current_position' => fake()->optional(0.7)->jobTitle()
        ];
    }
}
