<?php

namespace Database\Factories;

use App\Models\Certification;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issued = fake()->dateTimeBetween('-5 years', 'now');

        return [
            'student_id' => Student::factory(),
            'name' => fake()->randomElement(['AWS Certified', 'Cisco CCNA', 'Scrum Master', 'Google Analytics']),
            'issuing_authority' => fake()->company(),
            'date_issued' => $issued,
            'expiry_date' => fake()->optional(0.7)->dateTimeBetween($issued, '+5 years'),
            'verification_url' => fake()->optional()->url(),
            'credential_id' => fake()->optional()->bothify('CERT-####-????')
        ];
    }
}
