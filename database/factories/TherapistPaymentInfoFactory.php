<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class TherapistPaymentInfoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'   => User::where('role', UserRole::Therapist)->inRandomOrder()->first()->id,
            'iban'      => $this->faker->iban('FR'),
            'bic'       => $this->faker->swiftBicNumber(),
            'bank_name' => $this->faker->randomElement(['BNP Paribas', 'Crédit Agricole', 'Société Générale', 'LCL', 'Caisse d\'Épargne']),
        ];
    }
}
