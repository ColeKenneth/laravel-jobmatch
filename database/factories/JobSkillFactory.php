<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\JobSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobSkill>
 */
class JobSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_posting_id' => JobPosting::factory(),
            'skill_id' => Skill::factory(),
            'is_required' => fake()->boolean(80),
            'min_proficiency_level' => fake()->optional(0.7)->numberBetween(1, 5),
            'priority' => fake()->optional(0.7)->numberBetween(1, 5)
        ];
    }
}
