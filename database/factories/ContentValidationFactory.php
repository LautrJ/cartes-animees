<?php

namespace Database\Factories;

use App\Enums\ContentValidationStatus;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentValidationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submitted_by'     => User::therapists()->inRandomOrder()->first()->id,
            'reviewed_by'      => User::admins()->first()->id,
            'status'           => ContentValidationStatus::Approved,
            'rejection_reason' => null,
            'submitted_at'     => now()->subDays(rand(1, 30)),
            'reviewed_at'      => now()->subDays(rand(0, 5)),
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status'      => ContentValidationStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'           => ContentValidationStatus::Rejected,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
