<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentSkill>
 */
class StudentSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'skill_id' => Skill::factory(),
            'proficiency_level' => fake()->numberBetween(1, 5),
            'years_of_experience' => fake()->optional(0.7)->numberBetween(1, 5)
        ];
    }
}
