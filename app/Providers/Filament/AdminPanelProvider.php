<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use App\Filament\Widgets\PendingPayoutsWidget;
use App\Filament\Widgets\PendingValidationsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#a896d8'),
                'success' => Color::hex('#22c55e'),
                'warning' => Color::hex('#f97316'),
                'danger'  => Color::hex('#ef4444'),
                'info'    => Color::hex('#3b82f6'),
                'gray'    => Color::Slate,
            ])
            ->brandName('Cartes Animées')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                PendingValidationsWidget::class,
                PendingPayoutsWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => $this->renderThemeLink(),
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => $this->renderLoginShapes(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => $this->renderBodySetup(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => $this->renderCounterScript(),
            )
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

    private function renderLoginShapes(): HtmlString
    {
        // Formes absolues à l'intérieur de .fi-simple-layout.
        // Couleurs menthe hardcodées (#7ecfb3, #a8e0cc) sur fond lilas #f0ecfb.
        // opacity 0.13 max — effet subtil, pas agressif.
        return new HtmlString(<<<'HTML'
<div class="ca-login-shapes" aria-hidden="true">
    <svg class="ca-login-shape ca-login-shape-1" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="90" fill="#7ecfb3"/>
    </svg>
    <svg class="ca-login-shape ca-login-shape-2" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <polygon points="100,8 192,54 192,146 100,192 8,146 8,54" fill="#a8e0cc"/>
    </svg>
    <svg class="ca-login-shape ca-login-shape-3" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path d="M 100 12 Q 188 175 12 175 Z" fill="#7ecfb3"/>
    </svg>
    <svg class="ca-login-shape ca-login-shape-4" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <polygon points="100,10 190,74 158,190 42,190 10,74" fill="#a8e0cc"/>
    </svg>
    <svg class="ca-login-shape ca-login-shape-5" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path d="M 32 102 Q 2 52 62 18 Q 122 -16 162 38 Q 202 92 170 144 Q 138 196 78 182 Q 18 168 32 102 Z" fill="#7ecfb3"/>
    </svg>
    <svg class="ca-login-shape ca-login-shape-6" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <polygon points="100,10 190,100 100,190 10,100" fill="#a8e0cc"/>
    </svg>
</div>
HTML);
    }

    private function renderThemeLink(): HtmlString
    {
        $url = Vite::asset('resources/css/filament/admin-theme.css');

        return new HtmlString("<link rel=\"stylesheet\" href=\"{$url}\">");
    }

    private function renderBodySetup(): HtmlString
    {
        $role = auth()->user()?->role?->value ?? 'admin';
        $shapes = view('components.filament-bg-shapes')->render();

        return new HtmlString(
            "<script>document.body.classList.add('role-{$role}')</script>" . $shapes
        );
    }

    private function renderCounterScript(): HtmlString
    {
        return new HtmlString(<<<'HTML'
<script>
(function () {
    function animateCounter(el) {
        const original = el.textContent.trim();
        const num = parseFloat(original.replace(/[^\d.]/g, ''));
        if (isNaN(num) || num === 0) return;
        const prefix  = original.match(/^[^\d]*/)?.[0]  ?? '';
        const suffix  = original.match(/[^\d.]*$/)?.[0] ?? '';
        const isFloat = original.includes('.');
        const dec     = isFloat ? (original.split('.')[1]?.length ?? 0) : 0;
        const dur     = 1200;
        const t0      = performance.now();

        (function tick(now) {
            const p = Math.min((now - t0) / dur, 1);
            const e = 1 - Math.pow(1 - p, 3);
            const v = e * num;
            el.textContent = prefix + (isFloat ? v.toFixed(dec) : Math.floor(v).toLocaleString()) + suffix;
            p < 1 ? requestAnimationFrame(tick) : (el.textContent = original);
        })(t0);
    }

    function runCounters() {
        document.querySelectorAll('.fi-wi-stats-overview-stat-value').forEach(animateCounter);
    }

    document.addEventListener('DOMContentLoaded', runCounters);
    document.addEventListener('livewire:navigated', runCounters);
})();
</script>
HTML);
    }
}
