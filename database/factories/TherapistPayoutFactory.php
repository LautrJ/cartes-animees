<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class TherapistPayoutFactory extends Factory
{
    public function definition(): array
    {
        $monthsAgo   = rand(1, 6);
        $periodStart = now()->subMonths($monthsAgo)->startOfMonth();
        $periodEnd   = now()->subMonths($monthsAgo)->endOfMonth();

        return [
            'therapist_id'    => User::where('role', UserRole::Therapist)->inRandomOrder()->first()->id,
            'processed_by'    => User::where('role', UserRole::Admin)->first()->id,
            'amount'          => $this->faker->randomFloat(2, 10, 100),
            'commission_rate' => 2.00,
            'patient_count'   => $this->faker->numberBetween(1, 10),
            'period_start'    => $periodStart,
            'period_end'      => $periodEnd,
            'note'            => $this->faker->optional()->sentence(),
            'paid_at'         => now()->subDays(rand(1, 10)),
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'paid_at' => null,
        ]);
    }
}
