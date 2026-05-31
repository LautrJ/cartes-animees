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

        $activePatients = Child::whereHas('therapists', fn ($q) => $q
            ->where('users.id', $therapistId)
            ->whereNull('child_therapist.ended_at')
        )->count();

        $pendingValidations = ContentValidation::where('submitted_by', $therapistId)
            ->where('status', ContentValidationStatus::Pending)
            ->count();

        $currentRate = CommissionRateHistory::orderBy('effective_from', 'desc')
            ->first()?->rate ?? 0;

        $monthlyRevenue = number_format($activePatients * $currentRate, 2);

        $unlockedThisMonth = Child::whereHas('therapists', fn ($q) => $q
            ->where('users.id', $therapistId)
            ->whereNull('child_therapist.ended_at')
        )->whereHas('series', fn ($q) => $q
            ->where('child_series.unlocked_at', '>=', now()->startOfMonth())
        )->count();

        return [
            Stat::make(__('filament.therapist.widgets.therapist_stats_overview.active_patients_title'), $activePatients)
                ->description(__('filament.therapist.widgets.therapist_stats_overview.active_patients_description'))
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make(__('filament.therapist.widgets.therapist_stats_overview.unlocked_this_month_title'), $unlockedThisMonth)
                ->description(__('filament.therapist.widgets.therapist_stats_overview.unlocked_this_month_description'))
                ->color('info')
                ->icon('heroicon-o-lock-open'),

            Stat::make(__('filament.therapist.widgets.therapist_stats_overview.pending_validations_title'), $pendingValidations)
                ->description(__('filament.therapist.widgets.therapist_stats_overview.pending_validations_description'))
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make(__('filament.therapist.widgets.therapist_stats_overview.monthly_revenue_title'), $monthlyRevenue.' €')
                ->description(__('filament.therapist.widgets.therapist_stats_overview.monthly_revenue_description'))
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
