<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success($request->user());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['sometimes', 'string', 'max:100'],
        ]);

        $request->user()->update($validated);

        return ApiResponse::success($request->user()->fresh());
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return ApiResponse::error('Mot de passe actuel incorrect.', 422);
        }

        $request->user()->update([
            'password' => $request->password,
        ]);

        return ApiResponse::success(['message' => 'Mot de passe mis à jour avec succès.']);
    }
}
