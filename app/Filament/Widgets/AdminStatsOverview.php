<?php

namespace App\Filament\Widgets;

use App\Enums\ContentValidationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\ContentValidation;
use App\Models\Subscription;
use App\Models\TherapistPayout;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make(__('filament.widgets.admin_stats_overview.active_users_title'), User::where('is_active', true)->count())
                ->description(__('filament.widgets.admin_stats_overview.active_users_description'))
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make(__('filament.widgets.admin_stats_overview.active_subscriptions_title'), Subscription::where('status', SubscriptionStatus::Active)->count())
                ->description(__('filament.widgets.admin_stats_overview.active_subscriptions_description'))
                ->color('info')
                ->icon('heroicon-o-newspaper'),

            Stat::make(__('filament.widgets.admin_stats_overview.pending_validations_title'), ContentValidation::where('status', ContentValidationStatus::Pending)->count())
                ->description(__('filament.widgets.admin_stats_overview.pending_validations_description'))
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make(__('filament.widgets.admin_stats_overview.pending_payouts_title'), TherapistPayout::whereNull('paid_at')->count())
                ->description(__('filament.widgets.admin_stats_overview.pending_payouts_description'))
                ->color('danger')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
