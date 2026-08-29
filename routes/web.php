<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;

// ── Pont d'impersonation ──────────────────────────────────────────────────────
// Le package stechstudio/filament-impersonate redirige ici après avoir switché
// la session Laravel sur le compte de l'utilisateur impersonné.
// On génère un token Sanctum et on redirige vers la SPA Vue.
Route::get('/impersonate/bridge', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect('/admin');
    }

    // Orthophoniste → panel Filament therapist (comportement existant)
    if ($user->isTherapist()) {
        return redirect('/therapist');
    }

    // Parent → générer un token Sanctum et envoyer vers la SPA
    if ($user->isParent()) {
        $token = $user->createToken('impersonation')->plainTextToken;
        $returnUrl = urlencode('/filament-impersonate/leave');

        return redirect("/impersonate-parent?token={$token}&return_url={$returnUrl}");
    }

    return redirect('/admin');
})->middleware(['web', 'auth']);

// ── SPA Vue (catch-all — doit être en dernier) ────────────────────────────────
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
