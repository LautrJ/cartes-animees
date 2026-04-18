<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => User::where('role', UserRole::Parent)->inRandomOrder()->first()?->id
                ?? User::factory()->create(['role' => UserRole::Parent])->id,
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'birthdate'  => $this->faker->dateTimeBetween('-12 years', '-3 years')->format('Y-m-d'),
            'avatar'     => null,
            'notes'      => $this->faker->optional()->sentence(),
        ];
    }
}
