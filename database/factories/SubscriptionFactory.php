<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Child;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->subDays(rand(1, 30));

        return [
            'child_id' => Child::inRandomOrder()->first()->id,
            'overridden_by' => null,
            'stripe_subscription_id' => 'sub_'.$this->faker->unique()->bothify('??????????'),
            'stripe_price_id' => 'price_'.$this->faker->bothify('??????????'),
            'status' => SubscriptionStatus::Active,
            'override_price' => null,
            'current_period_start' => $start,
            'current_period_end' => $start->copy()->addMonth(),
            'canceled_at' => null,
        ];
    }

    public function free(): static
    {
        return $this->state([
            'stripe_subscription_id' => null,
            'stripe_price_id' => null,
            'status' => SubscriptionStatus::Free,
            'override_price' => 0.00,
        ]);
    }

    public function pastDue(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::PastDue,
        ]);
    }

    public function canceled(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::Canceled,
            'canceled_at' => now(),
        ]);
    }

    public function discounted(float $price): static
    {
        return $this->state([
            'override_price' => $price,
        ]);
    }
}
