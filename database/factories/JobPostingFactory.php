<?php

namespace Database\Factories;

use App\Enum\EmploymentType;
use App\Models\Employer;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $min = fake()->numberBetween(15000, 60000);
        $max = $min + fake()->numberBetween(5000, 40000);
        $posted = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'employer_id' => Employer::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->sentence(10),
            'preferred_skills' => fake()->optional(0.7)->sentence(8),
            'min_experience_years' => fake()->numberBetween(0, 5),
            'min_salary' => $min,
            'max_salary' => $max,
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'posted_date' => $posted,
            'deadline' => fake()->optional(0.8)->dateTimeBetween($posted, '+3 months'),
            'is_active' => fake()->boolean(80),
            'views' => 0,
            'applications' => 0
        ];
    }
}
