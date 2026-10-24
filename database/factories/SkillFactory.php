<?php

namespace Database\Factories;

use App\Enum\Category;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(Category::cases()),
            'ontology_id' => fake()->optional(0.8)->bothify('SKL-####'),
            'description' => fake()->optional(0.7)->sentence(12),
            'icon_url' => fake()->optional(0.5)->url()
        ];
    }
}
