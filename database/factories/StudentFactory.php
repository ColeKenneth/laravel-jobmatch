<?php

namespace Database\Factories;

use App\Enum\YearLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
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
            'student_id_number' => fake()->unique()->numerify('2#-QC#####'),
            'program' => fake()->randomElement(['BSIT', 'BSHM', 'BSN', 'BSMLS', 'BSBIO']),
            'year_level' => fake()->randomElement(YearLevel::cases()),
            'gwa' => fake()->randomFloat(2, 1.00, 5.00),
            'graduation_date' => null,
            'resume_file_path' => null,
            'resume_text' => null
        ];
    }
}
