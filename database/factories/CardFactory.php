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
            'created_by'   => User::where('role', UserRole::Admin)->first()->id,
            'name'         => ['fr' => $this->faker->word()],
            'gif_path'     => 'placeholders/placeholder.gif',
            'video_path'   => 'placeholders/placeholder.mp4',
            'sound_path'   => 'placeholders/placeholder.mp3',
            'width'        => 480,
            'height'       => 270,
            'duration'     => $this->faker->numberBetween(2, 10),
            'is_validated' => true,
        ];
    }

    public function unvalidated(): static
    {
        return $this->state(['is_validated' => false]);
    }
}
