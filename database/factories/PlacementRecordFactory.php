<?php

namespace Database\Factories;

use App\Enum\PlacementStatus;
use App\Models\JobPosting;
use App\Models\PlacementRecord;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlacementRecord>
 */
class PlacementRecordFactory extends Factory
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
            'job_posting_id' => JobPosting::factory(),
            'placement_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'placement_status' => fake()->randomElement(PlacementStatus::cases()),
            'notes' => fake()->optional(0.6)->sentence(10)
        ];
    }
}
