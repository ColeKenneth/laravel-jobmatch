<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employer>
 */
class EmployerFactory extends Factory
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
            'company_name' => fake()->company(),
            'industry' => fake()->randomElement(['Information Technology', 'Government', 'Software Development', 'Medicine', 'Law']),
            'website' => fake()->optional(0.8)->url(),
            'company_description' => fake()->optional(0.7)->paragraph(3),
            'logo_url' => fake()->url()
        ];
    }
}
