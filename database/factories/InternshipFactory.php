<?php

namespace Database\Factories;

use App\Models\Internship;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $position = fake()->randomElement([
            'Junior Software Developer',
            'IT Support',
            'Nurse Intern',
            'Pharmacist Intern',
            'Student Teacher',
        ]);

        $start = fake()->dateTimeBetween('-3 years', '-4 months');
        $startDate = Carbon::instance($start);
        $endDate = $startDate->copy()->addMonths(fake()->numberBetween(2, 6));

        return [
            'student_id' => Student::factory(),
            'company_name' => fake()->company(),
            'position' => $position,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'skills_gained' => fake()->optional(0.7)->sentence(6),
            'supervisor' => fake()->optional(0.9)->name(),
            'supervisor_email' => fake()->optional(0.9)->unique()->safeEmail()
        ];
    }
}
