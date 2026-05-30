<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role' => UserRole::Parent,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->optional()->phoneNumber(),
            'is_active' => true,
            'last_login_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    public function therapist(): static
    {
        return $this->state(['role' => UserRole::Therapist]);
    }

    public function admin(): static
    {
        return $this->state(['role' => UserRole::Admin]);
    }

    public function parent(): static
    {
        return $this->state(['role' => UserRole::Parent]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
            'last_login_at' => fake()->dateTimeBetween('-6 months', '-2 months'),
        ]);
    }

    public function noRecentLogin(): static
    {
        return $this->state([
            'last_login_at' => fake()->dateTimeBetween('-3 months', '-15 days'),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
