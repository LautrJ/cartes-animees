<?php

namespace Database\Seeders;

use App\Enums\ChildSeriesStatus;
use App\Enums\ContentValidationStatus;
use App\Models\Card;
use App\Models\Child;
use App\Models\CommissionRateHistory;
use App\Models\ContentValidation;
use App\Models\Invoice;
use App\Models\Series;
use App\Models\TherapistPaymentInfo;
use App\Models\TherapistPayout;
use App\Models\User;
use App\Services\StripeTestDataService;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function __construct(protected StripeTestDataService $stripeTestData) {}

    public function run(User $admin, string $priceId): void
    {
        // ----------------------------------------------------------------
        // Historique des taux de commission (données de démo)
        // ----------------------------------------------------------------
        CommissionRateHistory::insert([
            ['rate' => 1.50, 'effective_from' => now()->subMonths(12), 'created_by' => $admin->id, 'created_at' => now()->subMonths(12), 'updated_at' => now()->subMonths(12)],
            ['rate' => 2.00, 'effective_from' => now()->subMonths(8),  'created_by' => $admin->id, 'created_at' => now()->subMonths(8),  'updated_at' => now()->subMonths(8)],
            ['rate' => 1.75, 'effective_from' => now()->subMonths(4),  'created_by' => $admin->id, 'created_at' => now()->subMonths(4),  'updated_at' => now()->subMonths(4)],
            ['rate' => 2.00, 'effective_from' => now()->subMonth(),    'created_by' => $admin->id, 'created_at' => now()->subMonth(),    'updated_at' => now()->subMonth()],
        ]);

        // ----------------------------------------------------------------
        // Orthophonistes
        // ----------------------------------------------------------------
        $therapists = User::factory(3)->therapist()->create();

        $therapists->each(fn($therapist) =>
        TherapistPaymentInfo::factory()->create(['user_id' => $therapist->id])
        );

        // ----------------------------------------------------------------
        // Parents + enfants + liaisons orthophoniste
        // ----------------------------------------------------------------
        $parents = User::factory(5)->parent()->create();
        $parents = $parents->merge(User::factory(2)->parent()->noRecentLogin()->create());
        $children = collect();

        $parents->each(function ($parent) use ($therapists, &$children) {
            $parentChildren = Child::factory(2)->create(['parent_id' => $parent->id]);
            $children = $children->merge($parentChildren);

            $parentChildren->each(fn($child) =>
            $child->therapists()->attach(
                $therapists->random()->id,
                ['assigned_by' => null, 'assigned_at' => now()->subMonths(rand(1, 6)), 'ended_at' => null]
            )
            );
        });

        // ----------------------------------------------------------------
        // Abonnements via Stripe (vrais appels API sandbox)
        // 7 actifs, 1 gratuit, 1 past_due, 1 annulé
        // ----------------------------------------------------------------
        $children->take(7)->each(fn($child) =>
        $this->stripeTestData->createActiveSubscription(
            $child->parent, $child, $priceId
        )
        );

        $this->stripeTestData->createFreeSubscription($children->get(7), $admin);

        $this->stripeTestData->createPastDueSubscription(
            $children->get(8)->parent, $children->get(8), $priceId
        );

        $this->stripeTestData->createCanceledSubscription(
            $children->get(9)->parent, $children->get(9), $priceId
        );

        // ----------------------------------------------------------------
        // Factures (miroir local des événements Stripe)
        // ----------------------------------------------------------------
        $children->take(7)->each(function ($child) {
            $sub = $child->subscription;
            if ($sub) {
                Invoice::factory(rand(2, 4))->create(['subscription_id' => $sub->id]);
            }
        });

        $pastDueSub = $children->get(8)->subscription;
        if ($pastDueSub) {
            Invoice::factory()->open()->create(['subscription_id' => $pastDueSub->id]);
            Invoice::factory()->uncollectible()->create(['subscription_id' => $pastDueSub->id]);
        }

        // ----------------------------------------------------------------
        // Virements orthophonistes
        // ----------------------------------------------------------------
        $therapists->each(function ($therapist) use ($admin) {
            TherapistPayout::factory(2)->create([
                'therapist_id' => $therapist->id,
                'processed_by' => $admin->id,
            ]);
            TherapistPayout::factory()->pending()->create([
                'therapist_id' => $therapist->id,
                'processed_by' => $admin->id,
            ]);
        });

        // ----------------------------------------------------------------
        // Cartes
        // ----------------------------------------------------------------
        $cards = Card::factory(15)->create(['created_by' => $admin->id]);

        $therapists->take(2)->each(function ($therapist) {
            $pendingCards = Card::factory(2)->unvalidated()->byTherapist($therapist)->create();
            $pendingCards->each(fn($card) =>
            ContentValidation::factory()->pending()->create([
                'validatable_id'   => $card->id,
                'validatable_type' => Card::class,
                'submitted_by'     => $therapist->id,
            ])
            );
        });

        // ----------------------------------------------------------------
        // Séries
        // ----------------------------------------------------------------
        $baseSeries = Series::factory(3)->base()->create(['created_by' => $admin->id]);
        $baseSeries->each(fn($series) =>
        $series->cards()->attach(
            $cards->random(5)->pluck('id')->mapWithKeys(fn($id, $i) => [$id => ['order' => $i + 1]])
        )
        );

        $normalSeries = Series::factory(5)->create(['created_by' => $admin->id]);
        $normalSeries->each(fn($series) =>
        $series->cards()->attach(
            $cards->random(rand(3, 5))->pluck('id')->mapWithKeys(fn($id, $i) => [$id => ['order' => $i + 1]])
        )
        );

        $pendingSeries = Series::factory()->byTherapist($therapists->last())->create();
        ContentValidation::factory()->pending()->create([
            'validatable_id'   => $pendingSeries->id,
            'validatable_type' => Series::class,
            'submitted_by'     => $therapists->last()->id,
        ]);

        // ----------------------------------------------------------------
        // Progression des enfants — séries supplémentaires
        // (les séries de base sont déjà débloquées par le ChildObserver)
        // ----------------------------------------------------------------
        $now = now();
        $children->each(function ($child) use ($normalSeries, $now) {
            if (rand(0, 1)) {
                $extraSeries = $normalSeries->random(rand(1, 3));
                $therapist   = $child->activeTherapists()->first();

                $extraSeries->each(function ($series) use ($child, $now, $therapist) {
                    $unlockedAt  = $now->copy()->subDays(rand(1, 60));
                    $isCompleted = (bool) rand(0, 1);

                    $child->series()->attach($series->id, [
                        'unlocked_by'  => $therapist?->id,
                        'status'       => $isCompleted ? ChildSeriesStatus::Completed->value : ChildSeriesStatus::Unlocked->value,
                        'unlocked_at'  => $unlockedAt,
                        'completed_at' => $isCompleted ? $unlockedAt->copy()->addDays(rand(3, 14)) : null,
                    ]);
                });
            }
        });
    }
}
