<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Child;
use App\Models\ContentValidation;
use App\Models\Series;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1 admin
        User::create([
            'role'       => UserRole::Admin,
            'first_name' => 'Admin',
            'last_name'  => 'Cartes Animées',
            'email'      => 'admin@cartes-animees.test',
            'password'   => Hash::make('admin123'),
            'is_active'  => true,
        ]);

        // 2 orthophonistes
        $therapists = User::factory(2)->create([
            'role'     => UserRole::Therapist,
            'password' => Hash::make('password'),
        ]);

        // 3 parents avec 2 enfants chacun
        User::factory(3)->create([
            'role'     => UserRole::Parent,
            'password' => Hash::make('password'),
        ])->each(function ($parent) use ($therapists) {
            Child::factory(2)->create(['parent_id' => $parent->id])
                ->each(function ($child) use ($therapists) {
                    $child->therapists()->attach(
                        $therapists->random()->id,
                        [
                            'assigned_by' => null,
                            'assigned_at' => now(),
                            'ended_at'    => null,
                        ]
                    );
                });
        });

        // 10 cartes validées par l'admin
        $cards = Card::factory(10)->create();

        // 2 cartes soumises par un orthophoniste (en attente)
        $pendingCards = Card::factory(2)->unvalidated()->create([
            'created_by' => $therapists->first()->id,
        ]);
        $pendingCards->each(fn($card) => ContentValidation::factory()
            ->pending()
            ->create([
                'validatable_id'   => $card->id,
                'validatable_type' => Card::class,
                'submitted_by'     => $therapists->first()->id,
            ])
        );

        // 2 séries de base avec 5 cartes chacune
        $baseSeries = Series::factory(2)->base()->create();
        $baseSeries->each(fn($series) => $series->cards()->attach(
            $cards->random(5)->pluck('id'),
            ['order' => 0]
        ));

        // 3 séries normales validées avec 3 cartes chacune
        $normalSeries = Series::factory(3)->create();
        $normalSeries->each(fn($series) => $series->cards()->attach(
            $cards->random(3)->pluck('id'),
            ['order' => 0]
        ));

        // 1 série soumise par un orthophoniste (en attente)
        $pendingSeries = Series::factory()->unvalidated()->create([
            'created_by' => $therapists->last()->id,
        ]);
        ContentValidation::factory()->pending()->create([
            'validatable_id'   => $pendingSeries->id,
            'validatable_type' => Series::class,
            'submitted_by'     => $therapists->last()->id,
        ]);
    }
}
