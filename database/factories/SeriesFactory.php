<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeriesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by'      => User::where('role', UserRole::Admin)->first()->id,
            'name'            => ['fr' => $this->faker->words(3, true)],
            'description'     => ['fr' => $this->faker->sentence()],
            'thumbnail_path'  => null,
            'is_base'         => false,
            'is_validated'    => true,
            'is_active'       => true,
        ];
    }

    public function base(): static
    {
        return $this->state(['is_base' => true]);
    }

    public function unvalidated(): static
    {
        return $this->state(['is_validated' => false]);
    }
}
