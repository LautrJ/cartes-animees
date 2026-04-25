<?php

namespace Database\Seeders;

use App\Enums\ChildSeriesStatus;
use App\Enums\ContentValidationStatus;
use App\Enums\SettingType;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Card;
use App\Models\Child;
use App\Models\CommissionRateHistory;
use App\Models\ContentValidation;
use App\Models\Invoice;
use App\Models\Series;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\TherapistPaymentInfo;
use App\Models\TherapistPayout;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'role'       => UserRole::Admin,
            'first_name' => 'Admin',
            'last_name'  => 'Cartes Animées',
            'email'      => 'admin@cartes-animees.test',
            'password'   => Hash::make('admin123'),
            'is_active'  => true,
        ]);

        Setting::insert([
            [
                'key'         => 'commission_rate',
                'value'       => '2.00',
                'type'        => SettingType::Float->value,
                'label'       => 'Taux de commission',
                'description' => 'Montant en euros versé à l\'orthophoniste par patient actif par mois.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'subscription_price',
                'value'       => '9.99',
                'type'        => SettingType::Float->value,
                'label'       => 'Prix de l\'abonnement',
                'description' => 'Prix mensuel de l\'abonnement en euros.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        CommissionRateHistory::insert([
            ['rate' => 1.50, 'effective_from' => now()->subMonths(12), 'created_by' => $admin->id, 'created_at' => now()->subMonths(12), 'updated_at' => now()->subMonths(12)],
            ['rate' => 2.00, 'effective_from' => now()->subMonths(8),  'created_by' => $admin->id, 'created_at' => now()->subMonths(8),  'updated_at' => now()->subMonths(8)],
            ['rate' => 1.75, 'effective_from' => now()->subMonths(4),  'created_by' => $admin->id, 'created_at' => now()->subMonths(4),  'updated_at' => now()->subMonths(4)],
            ['rate' => 2.00, 'effective_from' => now()->subMonth(),    'created_by' => $admin->id, 'created_at' => now()->subMonth(),    'updated_at' => now()->subMonth()],
        ]);

        // 3 orthophonistes
        $therapists = User::factory(3)->create([
            'role'     => UserRole::Therapist,
            'password' => Hash::make('password'),
        ]);

        // Infos bancaires des orthophonistes
        $therapists->each(fn($therapist) =>
        TherapistPaymentInfo::factory()->create(['user_id' => $therapist->id])
        );

        // 5 parents avec 2 enfants chacun = 10 enfants
        $children = collect();
        User::factory(5)->create([
            'role'     => UserRole::Parent,
            'password' => Hash::make('password'),
        ])->each(function ($parent) use ($therapists, &$children) {
            $parentChildren = Child::factory(2)->create(['parent_id' => $parent->id]);
            $children = $children->merge($parentChildren);
            $parentChildren->each(function ($child) use ($therapists) {
                // Chaque enfant rattaché à un orthophoniste
                $child->therapists()->attach(
                    $therapists->random()->id,
                    ['assigned_by' => null, 'assigned_at' => now()->subMonths(rand(1, 6)), 'ended_at' => null]
                );
            });
        });

        // Abonnements
        $children->take(7)->each(fn($child) =>
        Subscription::factory()->create(['child_id' => $child->id])
        );
        Subscription::factory()->free()->create(['child_id' => $children->get(7)->id, 'overridden_by' => $admin->id]);
        Subscription::factory()->pastDue()->create(['child_id' => $children->get(8)->id]);
        Subscription::factory()->create(['child_id' => $children->get(9)->id]);

        // Factures
        Subscription::where('status', SubscriptionStatus::Active)->get()
            ->each(fn($sub) => Invoice::factory(rand(2, 4))->create(['subscription_id' => $sub->id]));
        $pastDue = Subscription::where('status', SubscriptionStatus::PastDue)->first();
        if ($pastDue) {
            Invoice::factory()->open()->create(['subscription_id' => $pastDue->id]);
            Invoice::factory()->uncollectible()->create(['subscription_id' => $pastDue->id]);
        }

        // Virements orthophonistes
        $therapists->each(function ($therapist) {
            TherapistPayout::factory(2)->create(['therapist_id' => $therapist->id]);
            TherapistPayout::factory()->pending()->create(['therapist_id' => $therapist->id]);
        });

        // 15 cartes validées par l'admin
        $cards = Card::factory(15)->create();

        // 3 cartes soumises par des orthophonistes (en attente)
        $therapists->take(2)->each(function ($therapist) use (&$cards) {
            $pendingCards = Card::factory(2)->unvalidated()->create(['created_by' => $therapist->id]);
            $pendingCards->each(fn($card) => ContentValidation::factory()->pending()->create([
                'validatable_id'   => $card->id,
                'validatable_type' => Card::class,
                'submitted_by'     => $therapist->id,
            ]));
        });

        // 3 séries de base avec 5 cartes chacune
        $baseSeries = Series::factory(3)->base()->create();
        $baseSeries->each(fn($series) => $series->cards()->attach(
            $cards->random(5)->pluck('id'),
            ['order' => 0]
        ));

        // 5 séries normales validées avec 3-5 cartes chacune
        $normalSeries = Series::factory(5)->create();
        $normalSeries->each(fn($series) => $series->cards()->attach(
            $cards->random(rand(3, 5))->pluck('id'),
            ['order' => 0]
        ));

        // 1 série soumise par un orthophoniste (en attente)
        $pendingSeries = Series::factory()->unvalidated()->create(['created_by' => $therapists->last()->id]);
        ContentValidation::factory()->pending()->create([
            'validatable_id'   => $pendingSeries->id,
            'validatable_type' => Series::class,
            'submitted_by'     => $therapists->last()->id,
        ]);

        // Progression des enfants — déblocage de séries
        $allSeries = $baseSeries->merge($normalSeries);
        $now = now();

        $children->each(function ($child) use ($allSeries, $now) {
            // Chaque enfant a accès aux séries de base automatiquement
            $baseSeries = $allSeries->where('is_base', true);
            $baseSeries->each(function ($series) use ($child, $now) {
                DB::table('child_series')->insertOrIgnore([
                    'child_id'     => $child->id,
                    'series_id'    => $series->id,
                    'unlocked_by'  => null,
                    'status'       => ChildSeriesStatus::Unlocked->value,
                    'unlocked_at'  => $now->copy()->subMonths(rand(1, 6)),
                    'completed_at' => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            });

            // 50% des enfants ont des séries supplémentaires débloquées
            if (rand(0, 1)) {
                $extraSeries = $allSeries->where('is_base', false)->random(rand(1, 3));
                $therapist = $child->therapists()->first();
                $extraSeries->each(function ($series) use ($child, $now, $therapist) {
                    $unlockedAt = $now->copy()->subDays(rand(1, 60));
                    $isCompleted = rand(0, 1);
                    DB::table('child_series')->insertOrIgnore([
                        'child_id'     => $child->id,
                        'series_id'    => $series->id,
                        'unlocked_by'  => $therapist?->id,
                        'status'       => $isCompleted ? ChildSeriesStatus::Completed->value : ChildSeriesStatus::Unlocked->value,
                        'unlocked_at'  => $unlockedAt,
                        'completed_at' => $isCompleted ? $unlockedAt->copy()->addDays(rand(3, 14)) : null,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                });
            }
        });
    }
}
