<?php

namespace App\Providers\Filament;

use App\Filament\Therapist\Widgets\CurrentCommissionRateWidget;
use App\Filament\Therapist\Widgets\TherapistPatientsWidget;
use App\Filament\Therapist\Widgets\TherapistStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TherapistPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('therapist')
            ->path('therapist')
            ->viteTheme('resources/css/filament/therapist/theme.css')
            ->brandName('Cartes Animées')
            ->colors([
                'primary' => Color::hex('#449b7f'),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->login()
            ->renderHook(
                'panels::topbar.end',
                fn() => auth()->user()?->canImpersonate()
                    ? new \Illuminate\Support\HtmlString(
                    '
                            <a href="/filament-impersonate/leave"
                               style="margin-right:1rem; font-size:0.875rem; color:#5ab99a; font-weight:500;">
                               ← Retour admin
                            </a>
                        ')
                    : ''
            )
            ->discoverResources(in: app_path('Filament/Therapist/Resources'), for: 'App\\Filament\\Therapist\\Resources')
            ->discoverPages(in: app_path('Filament/Therapist/Pages'), for: 'App\\Filament\\Therapist\\Pages')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Therapist/Widgets'), for: 'App\\Filament\\Therapist\\Widgets')
            ->widgets([
                CurrentCommissionRateWidget::class,
                TherapistStatsOverview::class,
                TherapistPatientsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
