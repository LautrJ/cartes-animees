<?php

namespace App\Filament\Widgets;

use App\Enums\ContentValidationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\ContentValidation;
use App\Models\Subscription;
use App\Models\TherapistPayout;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Utilisateurs actifs', User::where('is_active', true)->count())
                ->description('Tous rôles confondus')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Abonnements actifs', Subscription::where('status', SubscriptionStatus::Active)->count())
                ->description('Hors gratuits et annulés')
                ->color('info')
                ->icon('heroicon-o-newspaper'),

            Stat::make('Validations en attente', ContentValidation::where('status', ContentValidationStatus::Pending)->count())
                ->description('Contenus à traiter')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Paies en attente', TherapistPayout::whereNull('paid_at')->count())
                ->description('Virements à effectuer')
                ->color('danger')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
