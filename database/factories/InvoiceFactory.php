<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->subDays(rand(1, 60));

        return [
            'subscription_id'  => Subscription::inRandomOrder()->first()->id,
            'stripe_invoice_id' => 'in_' . $this->faker->unique()->bothify('??????????'),
            'amount'           => 9.99,
            'status'           => InvoiceStatus::Paid,
            'invoice_pdf'      => null,
            'period_start'     => $start,
            'period_end'       => $start->copy()->addMonth(),
            'paid_at'          => $start->copy()->addDays(rand(0, 3)),
        ];
    }

    public function open(): static
    {
        return $this->state([
            'status'  => InvoiceStatus::Open,
            'paid_at' => null,
        ]);
    }

    public function uncollectible(): static
    {
        return $this->state([
            'status'  => InvoiceStatus::Uncollectible,
            'paid_at' => null,
        ]);
    }
}
