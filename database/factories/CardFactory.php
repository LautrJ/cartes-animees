<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by'   => User::factory()->admin(),
            'name'         => ['fr' => $this->faker->word()],
            'drawn_animation_path'     => 'placeholders/placeholder.gif',
            'real_animation_path'   => 'placeholders/placeholder.mp4',
            'sound_path'   => 'placeholders/placeholder.mp3',
            'width'        => 480,
            'height'       => 270,
            'duration'     => $this->faker->numberBetween(2, 10),
            'is_validated' => true,
        ];
    }

    public function byTherapist(User $therapist): static
    {
        return $this->state(['created_by' => $therapist->id]);
    }

    public function unvalidated(): static
    {
        return $this->state(['is_validated' => false]);
    }
}
