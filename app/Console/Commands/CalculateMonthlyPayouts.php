<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\CommissionRateHistory;
use App\Models\TherapistPayout;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CalculateMonthlyPayouts extends Command
{
    protected $signature = 'payouts:calculate
                            {--month= : Mois à calculer (format Y-m, ex: 2026-04). Par défaut : mois précédent}
                            {--dry-run : Simuler sans insérer en base}';

    protected $description = 'Calcule les virements mensuels des orthophonistes';

    public function handle(): int
    {
        // ----------------------------------------------------------------
        // Déterminer la période
        // ----------------------------------------------------------------
        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth()
            : now()->subMonth()->startOfMonth();

        $periodStart = $month->copy()->startOfMonth();
        $periodEnd = $month->copy()->endOfMonth();
        $isDryRun = $this->option('dry-run');

        $this->info("Période : {$periodStart->format('d/m/Y')} → {$periodEnd->format('d/m/Y')}");

        if ($isDryRun) {
            $this->warn('Mode simulation — aucune donnée ne sera insérée.');
        }

        // ----------------------------------------------------------------
        // Récupérer le taux de commission en vigueur à la fin de la période
        // ----------------------------------------------------------------
        $commissionRate = CommissionRateHistory::where('effective_from', '<=', $periodEnd)
            ->orderBy('effective_from', 'desc')
            ->first();

        if (! $commissionRate) {
            $this->error('Aucun taux de commission trouvé.');

            return self::FAILURE;
        }

        $this->info("Taux de commission : {$commissionRate->rate} €/patient");

        // ----------------------------------------------------------------
        // Récupérer l'admin pour processed_by
        // ----------------------------------------------------------------
        $admin = User::admins()->first();

        if (! $admin) {
            $this->error('Aucun administrateur trouvé.');

            return self::FAILURE;
        }

        // ----------------------------------------------------------------
        // Calculer les payouts par orthophoniste
        // ----------------------------------------------------------------
        $therapists = User::therapists()->active()->get();
        $totalPayouts = 0;

        foreach ($therapists as $therapist) {
            // Patients rémunérateurs au dernier jour de la période :
            // - Suivi actif (ended_at null ou ended_at > fin de période)
            // - Abonnement active
            // - override_price null (prix normal) ou override_price > 0 (remise partielle)
            $patientCount = $therapist->patientsAsTherapist()
                ->wherePivot('assigned_at', '<=', $periodEnd)
                ->where(function ($query) use ($periodEnd) {
                    $query->whereNull('child_therapist.ended_at')
                        ->orWhere('child_therapist.ended_at', '>', $periodEnd);
                })
                ->whereHas('subscriptions', function ($query) {
                    $query->where('status', SubscriptionStatus::Active)
                        ->where(function ($q) {
                            $q->whereNull('override_price')
                                ->orWhere('override_price', '>', 0);
                        });
                })
                ->count();

            if ($patientCount === 0) {
                $this->line("{$therapist->getFilamentName()} — 0 patient rémunérateur, ignoré.");

                continue;
            }

            $amount = round($patientCount * $commissionRate->rate, 2);

            $this->line("{$therapist->getFilamentName()} — {$patientCount} patient(s) × {$commissionRate->rate} € = {$amount} €");

            if (! $isDryRun) {
                // Vérifier si un payout existe déjà pour cette période
                $existingPayout = TherapistPayout::where('therapist_id', $therapist->id)
                    ->whereDate('period_start', $periodStart->format('Y-m-d'))
                    ->whereDate('period_end', $periodEnd->format('Y-m-d'))
                    ->first();

                if ($existingPayout) {
                    $this->warn('Payout déjà existant pour cette période, ignoré.');

                    continue;
                }

                $payout = TherapistPayout::create([
                    'therapist_id' => $therapist->id,
                    'processed_by' => $admin->id,
                    'amount' => $amount,
                    'patient_count' => $patientCount,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'paid_at' => null,
                ]);

                // Notification in-app à l'orthophoniste
                Notification::make()
                    ->title("Virement de {$amount} € généré pour {$periodStart->format('m/Y')}.")
                    ->success()
                    ->sendToDatabase($therapist);

                $totalPayouts++;
            }
        }

        $this->newLine();
        $this->info("{$totalPayouts} virement(s) généré(s).");

        return self::SUCCESS;
    }
}
