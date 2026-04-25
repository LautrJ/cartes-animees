<?php

namespace App\Filament\Therapist\Widgets;

use App\Enums\ContentValidationStatus;
use App\Models\Child;
use App\Models\CommissionRateHistory;
use App\Models\ContentValidation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TherapistStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $therapistId = auth()->id();

        $activePatients = Child::whereHas('therapists', fn($q) => $q
            ->where('users.id', $therapistId)
            ->whereNull('child_therapist.ended_at')
        )->count();

        $pendingValidations = ContentValidation::where('submitted_by', $therapistId)
            ->where('status', ContentValidationStatus::Pending)
            ->count();

        $currentRate = CommissionRateHistory::orderBy('effective_from', 'desc')
            ->first()?->rate ?? 0;

        $monthlyRevenue = number_format($activePatients * $currentRate, 2);

        $unlockedThisMonth = Child::whereHas('therapists', fn($q) => $q
            ->where('users.id', $therapistId)
            ->whereNull('child_therapist.ended_at')
        )->whereHas('series', fn($q) => $q
            ->where('child_series.unlocked_at', '>=', now()->startOfMonth())
        )->count();

        return [
            Stat::make('Patients actifs', $activePatients)
                ->description('Suivis en cours')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Séries débloquées ce mois', $unlockedThisMonth)
                ->description('Nouveaux déblocages')
                ->color('info')
                ->icon('heroicon-o-lock-open'),

            Stat::make('Contenus en attente', $pendingValidations)
                ->description('En cours de validation')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Revenus estimés', $monthlyRevenue . ' €')
                ->description('Ce mois-ci')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
