<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionRateHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rate' => $this->faker->randomFloat(2, 1, 5),
            'effective_from' => now(),
            'created_by' => User::factory()->admin(),
        ];
    }
}
