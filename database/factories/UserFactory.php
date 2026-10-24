<?php

namespace Database\Factories;

use App\Enum\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.6)->lastName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(UserRole::cases()),
            'is_active' => fake()->boolean(90),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function student() : static
    {
        return $this->state(fn () => ['role' => UserRole::STUDENT]);
    }

    public function alumni() : static
    {
        return $this->state(fn () => ['role' => UserRole::ALUMNI]);
    }

    public function employer() : static
    {
        return $this->state(fn () => ['role' => UserRole::EMPLOYER]);
    }

    public function admin() : static
    {
        return $this->state(fn () => ['role' => UserRole::ADMIN]);
    }

    public function placementOfficer() : static
    {
        return $this->state(fn () => ['role' => UserRole::PLACEMENT_OFFICER]);
    }
}
