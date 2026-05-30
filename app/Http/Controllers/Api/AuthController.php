<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Notifications\WelcomeParentNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'role' => UserRole::Parent,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        $user->notify(new WelcomeParentNotification(
            firstName: $user->first_name,
        ));

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success(['token' => $token, 'user' => $user], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return ApiResponse::error('Identifiants invalides.', 401);
        }

        if (! $user->is_active) {
            return ApiResponse::error('Votre compte est désactivé.', 403);
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(['message' => 'Déconnecté avec succès.']);
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success($request->user());
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                // URL vers le frontend Vue.js (à adapter)
                $url = config('app.frontend_url').'/reset-password?token='.$token.'&email='.urlencode($user->email);

                $user->sendPasswordResetNotification($token);
            }
        );

        return ApiResponse::success(['message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ApiResponse::error('Token invalide ou expiré.', 422);
        }

        return ApiResponse::success(['message' => 'Mot de passe réinitialisé avec succès.']);
    }
}
