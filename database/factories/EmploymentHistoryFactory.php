<?php

namespace Database\Factories;

use App\Models\Alumni;
use App\Models\EmploymentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmploymentHistory>
 */
class EmploymentHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-15 years', '-6 months');
        $position = fake()->randomElement(['Junior Software Developer', 'IT Support', 'Nurse Intern', 'Pharmacist Intern', 'Student Teacher']);
        $isIntern = str_contains(strtolower($position), 'intern') || str_contains(strtolower($position), 'student teacher');
        $isCurrent = $isIntern ? false : fake()->boolean(70);

        return [
            'alumni_id' => Alumni::factory(),
            'company_name' => fake()->company(),
            'position' => $position,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $isCurrent ? null : fake()->dateTimeBetween($start, 'now')->format('Y-m-d'),
            'is_current' => $isCurrent,
            'responsibilities' => fake()->optional(0.6)->paragraph(2),
        ];
    }
}
