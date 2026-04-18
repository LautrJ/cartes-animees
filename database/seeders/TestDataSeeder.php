<?php

namespace Database\Seeders;

use App\Enums\SettingType;
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
            [
                'rate'           => 1.50,
                'effective_from' => now()->subMonths(12),
                'created_by'     => $admin->id,
                'created_at'     => now()->subMonths(12),
                'updated_at'     => now()->subMonths(12),
            ],
            [
                'rate'           => 2.00,
                'effective_from' => now()->subMonths(8),
                'created_by'     => $admin->id,
                'created_at'     => now()->subMonths(8),
                'updated_at'     => now()->subMonths(8),
            ],
            [
                'rate'           => 1.75,
                'effective_from' => now()->subMonths(4),
                'created_by'     => $admin->id,
                'created_at'     => now()->subMonths(4),
                'updated_at'     => now()->subMonths(4),
            ],
            [
                'rate'           => 2.00,
                'effective_from' => now()->subMonth(),
                'created_by'     => $admin->id,
                'created_at'     => now()->subMonth(),
                'updated_at'     => now()->subMonth(),
            ],
        ]);

        // 2 orthophonistes
        $therapists = User::factory(2)->create([
            'role'     => UserRole::Therapist,
            'password' => Hash::make('password'),
        ]);

        // 3 parents avec 2 enfants chacun
        $children = collect();
        User::factory(3)->create([
            'role'     => UserRole::Parent,
            'password' => Hash::make('password'),
        ])->each(function ($parent) use ($therapists, &$children) {
            $parentChildren = Child::factory(2)->create(['parent_id' => $parent->id]);
            $children = $children->merge($parentChildren);
            $parentChildren->each(function ($child) use ($therapists) {
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

        // Abonnements sur les enfants
        // 4 enfants avec abonnement actif
        $children->take(4)->each(fn($child) =>
            Subscription::factory()->create(['child_id' => $child->id])
        );

        // 1 enfant gratuit
        Subscription::factory()->free()->create([
            'child_id'      => $children->get(4)->id,
            'overridden_by' => $admin->id,
        ]);

        // 1 enfant en retard de paiement
        Subscription::factory()->pastDue()->create([
            'child_id' => $children->get(5)->id,
        ]);

        $activeSubscriptions = Subscription::where('status', 'active')->get();
        $activeSubscriptions->each(function ($subscription) {
            Invoice::factory(2)->create(['subscription_id' => $subscription->id]);
        });

        // 1 facture en retard sur l'abonnement past_due
        $pastDueSubscription = Subscription::where('status', 'past_due')->first();
        if ($pastDueSubscription) {
            Invoice::factory()->open()->create(['subscription_id' => $pastDueSubscription->id]);
            Invoice::factory()->uncollectible()->create(['subscription_id' => $pastDueSubscription->id]);
        }

        // Virements orthophonistes
        // 1 virement payé par orthophoniste
        $therapists->each(fn($therapist) =>
            TherapistPayout::factory()->create(['therapist_id' => $therapist->id])
        );

        // 1 virement en attente par orthophoniste
        $therapists->each(fn($therapist) =>
            TherapistPayout::factory()->pending()->create(['therapist_id' => $therapist->id])
        );

        // Infos bancaires des orthophonistes
        $therapists->each(fn($therapist) =>
            TherapistPaymentInfo::factory()->create(['user_id' => $therapist->id])
        );

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
